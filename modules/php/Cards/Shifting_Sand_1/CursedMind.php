<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\Globals;

class CursedMind extends BaseCard {

    public function castSpell($args) {
        Globals::setCursedMindIncreaseCost(1);
    }

    public function onBeforeCastSpell() {
        if (Globals::getCursedMindIncreaseCost() > 0) {
            $this->dealDamage(4);
            Globals::setCursedMindIncreaseCost(0);
        }
    }

}