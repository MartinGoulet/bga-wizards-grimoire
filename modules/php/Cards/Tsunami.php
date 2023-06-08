<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Objects\CardLocation;

class Tsunami extends BaseCard {

    public function castSpell($args) {
        // Gain 1 mana card. 
        // Deal 1 damage for each mana of a unique power in your hand
        $player_id = Game::get()->getPlayerId();

        $this->drawManaCards(1);

        $hand = Game::get()->deck_manas->getCardsInLocation(CardLocation::Hand(), $player_id);
        $distinct_values = array_unique(array_values(array_filter($hand, function($card) {
            return int($card['type']);
        })));

        $this->dealDamage(sizeof($distinct_values));
    }

}
