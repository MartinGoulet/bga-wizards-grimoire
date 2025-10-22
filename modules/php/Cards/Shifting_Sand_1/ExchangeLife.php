<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use Bga\Games\wizardsgrimoireext\Game;
use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\Players;

class ExchangeLife extends BaseCard {

    public function castSpell($args) {
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