<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\Globals;

class Haste extends BaseCard {

    public function castSpell($args)
    {
        // The next time you cast a spell this turn, it costs 2 less.
        Globals::setDiscountNextSpell(2);
    }

}
