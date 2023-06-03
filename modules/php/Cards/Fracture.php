<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Objects\CardLocation;

class Fracture extends BaseCard {

    public function castSpell($args) {
        // Gain 4 mana cards. 
        $this->drawManaCards(4);
        
        // TODO : You may move a mana card between 2 of your other spells
    }
}
