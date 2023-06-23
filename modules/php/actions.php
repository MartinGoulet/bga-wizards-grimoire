<?php

namespace WizardsGrimoire\Core;

use BgaSystemException;
use BgaUserException;
use WizardsGrimoire\Objects\CardLocation;

trait ActionTrait {

    //////////////////////////////////////////////////////////////////////////////
    //////////// Player actions
    //////////// 

    /*
        Each time a player is doing some game action, one of the methods below is called.
        (note: each method below must match an input method in nicodemus.action.php)
    */

    public function activateDelayedSpell(int $card_id, array $args) {
        $this->checkAction('activateDelayedSpell');
        $spell = SpellCard::isInRepertoire($card_id);
        $delayed_spells_ids = Globals::getCoolDownDelayedSpellIds();
        $is_authorize = in_array($spell['id'], $delayed_spells_ids);
        if (!$is_authorize) {
            throw new BgaSystemException("Card not authorize");
        }

        $filter = array_filter($delayed_spells_ids, function ($ids) use ($spell) {
            return $ids != $spell['id'];
        });
        Globals::setCoolDownDelayedSpellIds($filter);

        $cardClass = SpellCard::getInstanceOfCard($spell);
        $cardClass->castSpell($args);

        if (Players::getPlayerLife(Players::getOpponentId()) <= 0) {
            $this->gamestate->nextState('dead');
        } else if (sizeof($filter) > 0) {
            $this->gamestate->nextState('cast');
        } else {
            $this->gamestate->nextState('pass');
        }
    }

    public function discardMana(array $card_ids) {
        $this->checkAction('discardMana');

        $hand_count = ManaCard::getHandCount();
        if ($hand_count - sizeof($card_ids) !== 10) {
            throw new BgaSystemException("Not enough mana discarded");
        }

        $cards_before = [];
        $cards_after = [];
        foreach ($card_ids as $card_id) {
            $cards_before[] = ManaCard::isInHand($card_id);
            ManaCard::addOnTopOfDiscard($card_id);
            $cards_after[] = ManaCard::get($card_id);
        }
        Notifications::moveManaCard(Players::getPlayerId(), $cards_before, $cards_after);

        $this->gamestate->nextState();
    }

    public function chooseSpell(int $card_id) {
        $this->checkAction('chooseSpell');

        $card = SpellCard::get($card_id);

        if ($card == null) {
            throw new \BgaSystemException('Card is null');
        } else if ($card['location'] !== 'slot') {
            throw new \BgaUserException('Card is not in the spell pool');
        }

        $playerId = intval($this->getActivePlayerId());
        $cardDestination = CardLocation::PlayerSpellRepertoire($playerId);

        $nbr_spells = $this->deck_spells->countCardInLocation($cardDestination);

        if ($nbr_spells >= 6) {
            throw new \BgaSystemException("Use replaceSpell action");
        }

        $this->deck_spells->moveCard(
            $card_id,
            $cardDestination,
            $nbr_spells + 1
        );

        $newSpell = $this->deck_spells->pickCardForLocation(
            CardLocation::Deck(),
            CardLocation::SpellSlot(),
            $card['location_arg']
        );

        $card = SpellCard::get($card_id);

        Notifications::chooseSpell($playerId, $card);
        Notifications::refillSpell($playerId, $newSpell);

        $turn_number = Game::get()->getStat(WG_STAT_TURN_NUMBER);
        if ($turn_number <= 3) {
            $this->gamestate->nextState('next_player');
        } else {
            $this->gamestate->nextState('end');
        }
    }

    public function replaceSpell(int $old_card_id, int $new_card_id) {
        $this->checkAction('replaceSpell');

        $old_card = SpellCard::get($old_card_id);
        $new_card = SpellCard::get($new_card_id);
        $player_id = Players::getPlayerId();
        $spell_count = Game::get()->deck_spells->countCardInLocation(CardLocation::PlayerSpellRepertoire($player_id));

        if ($spell_count < 6) {
            throw new BgaSystemException("Use chooseSpell action");
        } elseif ($old_card == null || $new_card == null) {
            throw new BgaSystemException("Cards not found");
        }

        SpellCard::replaceSpell($old_card, $new_card);

        $this->gamestate->nextState('end');
    }

    public function castSpell(int $card_id, $args) {
        $this->checkAction('castSpell');
        $player_id = intval($this->getActivePlayerId());
        // Get the card and verify ownership
        $spell = SpellCard::isInRepertoire($card_id, $player_id);
        $mana_ids = array_shift($args);
        $mana_ids = explode(',', $mana_ids);

        $card_type = SpellCard::getCardInfo($spell);
        $cost = intval($card_type['cost']);

        $cost = max($cost - Globals::getDiscountNextSpell(), 0);
        Globals::setDiscountNextSpell(0);
        if ($card_type['type'] == WG_SPELL_TYPE_ATTACK) {
            $cost = max($cost - Globals::getDiscountNextSpell(), 0);
            Globals::setDiscountAttackSpell(0);
        }

        if ($cost == 0 && sizeof($mana_ids) == 1 && $mana_ids[0] == "") {
            // Free card
        } else if (sizeof($mana_ids) !== $cost) {
            throw new BgaSystemException("Not the right amount of mana required" . sizeof($mana_ids) . " : " . $cost);
        }

        if ($cost > 0) {
            $mana_cards_before = array_map(function ($mana_id) use ($player_id) {
                return ManaCard::isInHand($mana_id, $player_id);
            }, $mana_ids);

            // Move card to the mana position below the spell
            foreach ($mana_cards_before as $card_id => $card) {
                ManaCard::addOnTopOfManaCoolDown($card['id'], intval($spell['location_arg']));
            }

            $mana_cards_after = ManaCard::getCards($mana_ids);
        } else {
            $mana_cards_before = [];
            $mana_cards_after = [];
        }

        Notifications::castSpell($player_id, $card_type['name'], $mana_cards_before, $mana_cards_after);

        switch ($card_type['type']) {
            case WG_SPELL_TYPE_ATTACK:
                Globals::incConsecutivelyAttackSpellCount(1);
                break;

            default:
                Globals::setConsecutivelyAttackSpellCount(0);
                break;
        }

        Globals::setPreviousSpellPlayed(Globals::getSpellPlayed());
        Globals::setSpellPlayed(intval($spell['id']));

        switch ($card_type['activation']) {
            case WG_SPELL_ACTIVATION_ONGOING:
                $cardClass = SpellCard::getInstanceOfCard($spell);
                $cardClass->isOngoingSpellActive(true);
                $this->gamestate->nextState("cast");
                break;

            case WG_SPELL_ACTIVATION_INSTANT:
                $cardClass = SpellCard::getInstanceOfCard($spell);
                // Execute the ability of the card
                $cardClass->castSpell($args);

                if (Globals::getSkipInteraction()) {
                    Globals::setSkipInteraction(false);
                    $this->castOrEndGame();
                    return;
                }

                $card_interaction = $card_type['interaction'] ?? "";

                switch ($card_interaction) {
                    case 'player':
                        Globals::setInteractionPlayer(Players::getPlayerId());
                        $this->gamestate->nextState("player");
                        break;

                    case 'opponent':
                        Globals::setInteractionPlayer(Players::getOpponentId());
                        $this->gamestate->nextState("opponent");
                        break;

                    default:
                        $this->castOrEndGame();
                        break;
                }
                break;

            default:
                $this->gamestate->nextState("cast");
                break;
        }
    }

    private function castOrEndGame() {
        if (Players::getPlayerLife(Players::getOpponentId()) <= 0) {
            $this->gamestate->nextState('dead');
        } else {
            $this->gamestate->nextState('cast');
        }
    }

    public function castSpellInteraction($args) {
        $this->checkAction('castSpellInteraction');
        $player_id = Players::getPlayerId();
        // Get the card and verify ownership
        $spell = SpellCard::isInRepertoire(Globals::getSpellPlayed(), $player_id);

        $cardClass = SpellCard::getInstanceOfCard($spell);
        // Execute the ability of the card
        $cardClass->castSpellInteraction($args);

        if (Players::getPlayerLife(Players::getOpponentId()) <= 0) {
            $this->gamestate->nextState('dead');
        } else {
            $this->gamestate->nextState('return');
        }
    }

    public function basicAttack(int $mana_id) {
        $this->checkAction('basicAttack');
        $player_id = Players::getPlayerId();
        $card = ManaCard::isInHand($mana_id, $player_id);
        $damage = ManaCard::getPower($card);

        // Puppetmaster verification
        if (Globals::getIsActivePuppetmaster() && Globals::getPreviousBasicAttackPower() != $damage) {
            throw new BgaUserException("The power not match the previous attack");
        }

        Globals::setCurrentBasicAttackPower($damage);

        Game::get()->deck_manas->moveCard($mana_id, CardLocation::BasicAttack());
        $card_after = ManaCard::get($mana_id);
        Notifications::basicAttackCard($player_id, $card);
        Notifications::moveManaCard($player_id, [$card], [$card_after], "@@@", false);

        $this->gamestate->nextState(Globals::getIsActiveBattleVision() ? "battle_vision" : "attack");
    }

    public function blockBasicAttack($mana_id) {
        $this->checkAction('blockBasicAttack');
        $card = ManaCard::isInHand($mana_id);
        $damage = ManaCard::getPower($card);

        if ($damage == Globals::getCurrentBasicAttackPower()) {
            ManaCard::addOnTopOfDiscard($mana_id);
            $card_after = ManaCard::get($mana_id);
            Notifications::moveManaCard(Players::getPlayerId(), [$card], [$card_after], "@@@", false);
            Game::get()->gamestate->nextState("block");
        } else {
            throw new BgaUserException(_("Wrong Mana Power"));
        }
    }

    public function pass() {
        $this->checkAction('pass');

        if($this->gamestate->state_id() == ST_BASIC_ATTACK) {
            Globals::setPreviousBasicAttackPower(0);
        }

        $this->gamestate->nextState('pass');
    }

    //////////////////////////////////////////////////////////////////////////////
    //////////// Private method
    //////////// 

}
