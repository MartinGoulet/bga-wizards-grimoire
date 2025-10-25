<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_2;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\Globals;

class Premonition extends BaseCard {

    public function castSpell($args) {
        Globals::setDiscountPremonition(2);
    }

}