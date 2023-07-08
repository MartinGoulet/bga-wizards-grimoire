<?php

namespace WizardsGrimoire\Core;

use WizardsGrimoire\Cards\KickStarter_1\Lullaby;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Notifications;

trait StateTrait {

    //////////////////////////////////////////////////////////////////////////////
    //////////// Game state actions
    ////////////

    /*
        Here, you can create methods defined as "game state actions" (see "action" property in states.inc.php).
        The action method of state X is called everytime the current game state is set to X.
    */

    function stNewTurn() {
        $player_id = intval($this->getActivePlayerId());
        $this->setGameStateValue(WG_VAR_CURRENT_PLAYER, $player_id);

        $this->incStat(1, WG_STAT_TURN_NUMBER);
        $this->incStat(1, WG_STAT_TURN_NUMBER, $player_id);

        Globals::resetOnNewTurn();
        Events::onPlayerNewTurn();

        Lullaby::check();

        $next_state = ManaCard::getHandCount() > 10 ? "discard" : "spell";
        $this->gamestate->nextState($next_state);
    }

    function stSpellCoolDown() {
        Lullaby::check();

        $player_id = intval($this->getActivePlayerId());

        $cards_before = [];
        $spell_delayed = [];

        for ($i = 1; $i <= 6; $i++) {
            $mana_card = ManaCard::getOnTopOnManaCoolDown($i);
            if ($mana_card) {
                $spell = SpellCard::getFromRepertoire($i);
                $spell_info = SpellCard::getCardInfo($spell);
                $instance = SpellCard::getInstanceOfCard($spell);

                switch ($spell_info['activation']) {
                    case WG_SPELL_ACTIVATION_DELAYED:
                        if ($spell_info['activation_auto'] == true) {
                            $instance->castSpell($mana_card);
                        } else {
                            if ($instance->isDelayedSpellTrigger()) {
                                $spell_delayed[] = $spell['id'];
                            }
                        }
                        break;

                    case WG_SPELL_ACTIVATION_ONGOING:
                        if (ManaCard::countOnTopOfManaCoolDown($i) == 1) {
                            $instance->isOngoingSpellActive(false);
                        }
                        break;

                    default:
                        # code...
                        break;
                }
                $cards_before[] = $mana_card;
                ManaCard::addOnTopOfDiscard($mana_card['id']);
            }
        }

        Notifications::moveManaCard($player_id, $cards_before, false);

        if (sizeof($spell_delayed) == 0) {
            $this->gamestate->nextState('next');
        } else {
            Globals::setCoolDownDelayedSpellIds($spell_delayed);
            $this->gamestate->nextState('delayed');
        }
    }

    function stGainMana() {
        ManaCard::draw(3);

        Lullaby::check();
        $this->gamestate->nextState();
    }

    function stSwitchToOpponent() {
        Game::get()->gamestate->changeActivePlayer(Globals::getInteractionPlayer());
        Game::get()->gamestate->nextState();
    }

    function stReturnToCurrentPlayer() {
        if (Globals::getInteractionPlayer() != Players::getPlayerId()) {
            Game::get()->gamestate->changeActivePlayer(Players::getPlayerId());
        }
        Game::get()->gamestate->nextState();
    }

    function stActivateInteractivePlayer() {
        $player_id = Globals::getInteractionPlayer();
        $player_id = Players::getOpponentId();
        $this->gamestate->setPlayersMultiactive([$player_id], 'next', true);
    }

    function stBasicAttackDamage() {
        Lullaby::check();

        $opponent_id = Players::getOpponentId();
        $damage = Globals::getCurrentBasicAttackPower();

        $card = ManaCard::getBasicAttack();

        $life_remaining = Players::dealDamage($damage, $opponent_id);
        Notifications::basicAttack($opponent_id, $damage, $life_remaining);

        Globals::setPreviousBasicAttackPower(Globals::getCurrentBasicAttackPower());
        Globals::setCurrentBasicAttackPower(0);

        if (Players::getPlayerLife(Players::getOpponentId()) <= 0) {
            $this->gamestate->nextState('dead');
        } else {
            $this->gamestate->nextState('attack');
        }
    }

    function stBasicAttackEnd() {
        Lullaby::check();
        $card = ManaCard::getBasicAttack();

        if (Globals::getIsActivePowerHungry()) {
            ManaCard::addToHand($card['id'], Globals::getIsActivePowerHungryPlayer());
            Notifications::moveManaCard(Players::getPlayerId(), [$card], false);
            Events::onAddCardToHand();
        } else {
            ManaCard::addOnTopOfDiscard($card['id']);
            Notifications::moveManaCard(Players::getPlayerId(), [$card], false);
        }


        $this->gamestate->nextState();
    }

    function stNextPlayer() {
        Lullaby::check();

        $cards = SpellCard::getOngoingActiveSpells(Players::getPlayerId());
        foreach ($cards as $card_id => $card) {
            $instance = SpellCard::getInstanceOfCard($card);
            if (method_exists($instance, 'onTurnEnd')) {
                $instance->onTurnEnd();
            }
        }

        $this->giveExtraTime(Players::getPlayerId());

        $player_id = intval($this->getActivePlayerId());
        if ($player_id == Players::getPlayerId()) {
            $this->activeNextPlayer();
        }

        $this->gamestate->nextState();
    }

    public function stPreEndOfGame() {
        $this->gamestate->nextState();
    }
}
