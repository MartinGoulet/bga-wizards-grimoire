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
        for ($i = 1; $i < 6; $i++) {
            $location_from = CardLocation::PlayerManaCoolDown($playerId, $i);
            $mana_card = $this->deck_manas->getCardOnTop($location_from);
            if($mana_card) {
                $cards_before[] = $mana_card;
                $this->deck_manas->insertCardOnExtremePosition($mana_card['id'], CardLocation::Discard(), true);
                $cards_after[] = $this->deck_manas->getCard($mana_card['id']);
            }
        }

        Notifications::moveManaCard($playerId, $cards_before, $cards_after, "@@@", false);
        $this->gamestate->nextState();
    }

    function stGainMana() {
        $playerId = intval($this->getActivePlayerId());
        $mana_cards = $this->deck_manas->pickCards(3, CardLocation::Deck(), $playerId);

        Notifications::drawManaCards($playerId, $mana_cards);
        $this->gamestate->nextState();
    }

    function stNextPlayer() {
        $playerId = intval($this->getActivePlayerId());
        $this->giveExtraTime($playerId);
        $this->activeNextPlayer();

        $this->gamestate->nextState();
    }
}
