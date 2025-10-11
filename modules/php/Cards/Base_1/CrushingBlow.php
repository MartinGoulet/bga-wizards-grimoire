<?php

namespace WizardsGrimoireExt\Cards\Base_1;

use WizardsGrimoireExt\Cards\BaseCard;

class CrushingBlow extends BaseCard {

    public function castSpell($args) {
        // Deal 6 damage
        $this->dealDamage(6);
    }
}
