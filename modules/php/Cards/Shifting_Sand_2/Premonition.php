<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Globals;

class Premonition extends BaseCard {

    public function castSpell($args) {
        Globals::setDiscountPremonition(2);
    }

}