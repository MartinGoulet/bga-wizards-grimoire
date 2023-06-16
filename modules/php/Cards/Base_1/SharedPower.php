<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class SharedPower extends BaseCard {

    public function castSpell($args) {

        // You may give your opponent 1 mana card from your hand. 
        // If you do, gain 4 mana cards
        if(sizeof($args) == 0) {
            Notifications::spellNoEffect();
            return;
        }

        $mana_id = array_shift($args);
        $opponent_id = Players::getOpponentId();

        $card = ManaCard::isInHand($mana_id);

        // Give opponent 1 mana
        
        Game::get()->deck_manas->moveCard($mana_id, CardLocation::Hand(), $opponent_id);
        $cardAfter = ManaCard::get($mana_id);
        Notifications::moveManaCard($opponent_id, [$card], [$cardAfter]);

        // Gain 4 mana cards
        $this->drawManaCards(4);
    }
}