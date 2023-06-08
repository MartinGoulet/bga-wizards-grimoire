<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class Hellstorm extends BaseCard {

    public function castSpell($args) {
        // Gain 5 mana cards. Deal damage equal to the highest power mana you gain
        $player_id = Players::getPlayerId();
        $cards = $this->drawManaCards(5);

        $max_value = Game::getMaxManaCardValue($cards);
        $this->dealDamage($max_value);
    }

}
