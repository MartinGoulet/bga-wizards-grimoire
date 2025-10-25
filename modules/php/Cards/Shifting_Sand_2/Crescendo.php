<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_2;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\Globals;

class Crescendo extends BaseCard {

    public function castSpell($args) {
        Globals::setCrescendoIncreaseCost(1);
    }

    public function onBeforeCastSpell() {
        if (Globals::getCrescendoIncreaseCost() > 0) {
            $this->drawManaCards(4);
            Globals::setCrescendoIncreaseCost(0);
        }
    }
}
