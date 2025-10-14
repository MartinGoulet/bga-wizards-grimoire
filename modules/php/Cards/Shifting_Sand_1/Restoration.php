<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\Globals;

class Restoration extends BaseCard {

    public function castSpell($args) {
        // Gain 4 mana cards. Gain 1 additionnal mana card for each attack spell cas consecutively before this spell
        $count = 4 + Globals::getConsecutivelyAttackSpellCountBefore();
        $this->drawManaCards($count);
    }
}
