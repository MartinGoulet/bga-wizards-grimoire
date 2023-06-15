<?php

namespace WizardsGrimoire\Core;

use BgaSystemException;
use WizardsGrimoire\Objects\CardLocation;

trait ActionTrait {

    //////////////////////////////////////////////////////////////////////////////
    //////////// Player actions
    //////////// 

    /*
        Each time a player is doing some game action, one of the methods below is called.
        (note: each method below must match an input method in nicodemus.action.php)
    */

    public function discardMana(array $card_ids) {
        $this->checkAction('discardMana');

        $hand_count = ManaCard::getHandCount();
        if ($hand_count - sizeof($card_ids) !== 10) {
            throw new BgaSystemException("Not enough mana discarded");
        }

        foreach ($card_ids as $card_id) {
            ManaCard::isInHand($card_id);
            ManaCard::addOnTopOfDiscard($card_id);
        }

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

        // Discard old spell
        Game::get()->deck_spells->insertCardOnExtremePosition($old_card_id, CardLocation::Discard(), true);
        $discarded_card = SpellCard::get($old_card_id);
        Notifications::discardSpell($player_id, $discarded_card);

        // Choose spell
        $this->deck_spells->moveCard(
            $new_card_id,
            CardLocation::PlayerSpellRepertoire($player_id),
            $old_card['location_arg']
        );

        $card = SpellCard::get($new_card_id);
        Notifications::chooseSpell($player_id, $card);

        $newSpell = Game::get()->deck_spells->pickCardForLocation(
            CardLocation::Deck(),
            CardLocation::SpellSlot(),
            $new_card['location_arg'],
        );

        Notifications::refillSpell($player_id, $newSpell);

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
        if ($card_type['type'] == WG_SPELL_TYPE_DAMAGE) {
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

        if ($card_type['activation'] == WG_SPELL_ACTIVATION_INSTANT) {
            Globals::setSpellPlayed(intval($spell['id']));
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
        } else {
            $this->gamestate->nextState("cast");
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
        $opponent_id = Players::getOpponentId();
        $card = ManaCard::isInHand($mana_id, $player_id);
        $damage = intval($card['type']);

        ManaCard::addOnTopOfDiscard($mana_id);
        $card_after = ManaCard::get($mana_id);
        Notifications::moveManaCard($player_id, [$card], [$card_after], "", false);

        $life_remaining = Players::dealDamage($damage, $opponent_id);
        Notifications::basicAttack($opponent_id, $damage, $life_remaining);

        $player_ongoing_spells = SpellCard::getOngoingActiveSpells($player_id);
        $opponent_ongoing_spells = SpellCard::getOngoingActiveSpells($opponent_id);

        foreach ($player_ongoing_spells as $card_id => $card) {
            $spell = SpellCard::getInstanceOfCard($card);
            $spell->owner = $player_id;
            $spell->onAfterBasicAttack($mana_id);
        }

        foreach ($opponent_ongoing_spells as $card_id => $card) {
            $spell = SpellCard::getInstanceOfCard($card);
            $spell->owner = $opponent_id;
            $spell->onAfterBasicAttack($mana_id);
        }

        if (Players::getPlayerLife(Players::getOpponentId()) <= 0) {
            $this->gamestate->nextState('dead');
        } else {
            $this->gamestate->nextState('attack');
        }
    }

    public function pass() {
        $this->checkAction('pass');
        $this->gamestate->nextState('pass');
    }

    //////////////////////////////////////////////////////////////////////////////
    //////////// Private method
    //////////// 

}
