<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class Renewal extends BaseCard {

    public function castSpell($args) {
        // Gain mana until you have 4 mana cards in your hand
        $hand_count = Players::getHandCount();
        
        $nbr_cards = max([4 - $hand_count, 0]);
        $this->drawManaCards($nbr_cards);
    }

}
