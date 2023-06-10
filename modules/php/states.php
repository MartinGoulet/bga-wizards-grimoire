<?php

namespace WizardsGrimoire\Core;

use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Objects\CardLocation;

trait StateTrait {

    //////////////////////////////////////////////////////////////////////////////
    //////////// Game state actions
    ////////////

    /*
        Here, you can create methods defined as "game state actions" (see "action" property in states.inc.php).
        The action method of state X is called everytime the current game state is set to X.
    */

    function stNewTurn() {
        $playerId = intval($this->getActivePlayerId());

        $this->incStat(1, WG_STAT_TURN_NUMBER);
        $this->incStat(1, WG_STAT_TURN_NUMBER, $playerId);

        $this->gamestate->nextState();
    }

    function stSpellCoolDown() {
        $playerId = intval($this->getActivePlayerId());

        $cards_before = [];
        $cards_after = [];
        $spell_delayed = [];

        for ($i = 1; $i <= 6; $i++) {
            $mana_card = ManaCard::getOnTopOnManaCoolDown($i);
            if ($mana_card) {
                $spell = SpellCard::getCardInRepertoire($i);
                $spell_info = SpellCard::getCardInfo($spell);
                if ($spell_info['activation'] == WG_SPELL_ACTIVATION_DELAYED) {
                    if($spell_info['activation_auto'] == true) {
                        $instance = SpellCard::getInstanceOfCard($spell);
                        $instance->castSpell($mana_card);
                    } else {
                        $spell_delayed[] = $spell['id'];
                    }
                }
                $cards_before[] = $mana_card;
                ManaCard::addOnTopOfDiscard($mana_card['id']);
                $cards_after[] = ManaCard::get($mana_card['id']);
            }
        }

        Notifications::moveManaCard($playerId, $cards_before, $cards_after, "@@@", false);

        if(sizeof($spell_delayed) == 0) {
            $this->gamestate->nextState('next');
        } else {
            Game::setGlobalVariable(WG_GV_COOLDOWN_DELAYED_SPELLS, $spell_delayed);
            $this->gamestate->nextState('delayed');
        }

    }

    function stGainMana() {
        ManaCard::Draw(3);
        $this->gamestate->nextState();
    }

    function stNextPlayer() {
        $playerId = intval($this->getActivePlayerId());
        $this->giveExtraTime($playerId);
        $this->activeNextPlayer();

        $this->gamestate->nextState();
    }
}
