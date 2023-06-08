<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\Assert;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class SharedPower extends BaseCard {

    public function castSpell($args) {

        // You may give your opponent 1 mana card from your hand. 
        // If you do, gain 4 mana cards
        $mana_id = array_shift($args);
        $player_id = Players::getPlayerId();
        $opponent_id = Players::getOpponentId();
        $manaDeck = Game::get()->deck_manas;

        if (!$mana_id) return;

        $card = Assert::isCardInHand($mana_id, $player_id);

        // Give opponent 1 mana
        $manaDeck->moveCard($mana_id, CardLocation::Hand(), $opponent_id);
        $cardAfter = $manaDeck->getCard($mana_id);
        Notifications::moveManaCard($opponent_id, [$card], [$cardAfter]);

        // Gain 4 mana cards
        $this->drawManaCards(4);
    }
}
