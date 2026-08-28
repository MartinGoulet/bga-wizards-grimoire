<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_1;

use Bga\Games\WizardsGrimoire\Game;
use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class ExchangeLife extends BaseCard {

    public function castSpell($args) {

        $this->dealDamage(3);
        
        $exchangeHand = intval(array_shift($args)) == 1;

        if($exchangeHand) {
            $playerHand = ManaCard::getHand(Players::getPlayerId());
            $opponentHand = ManaCard::getHand(Players::getOpponentId());

            Notifications::exchangeManaHands(Players::getPlayerId());

            ManaCard::addCardsToHand($playerHand, Players::getOpponentId());
            ManaCard::addCardsToHand($opponentHand, Players::getPlayerId());

            Game::get()->undoSavepoint();
        } else {
            //Do nothing
        }
    }

}