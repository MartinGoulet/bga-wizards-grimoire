<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Objects\CardLocation;

class CrushingBlow extends BaseCard {

    public function castSpell($args) {
        // Deal 6 damage
        $this->dealDamage(6);
    }

}
