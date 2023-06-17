<?php

namespace WizardsGrimoire\Cards\KickStarter_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Globals;

class Amnesia extends BaseCard {

    public function castSpell($args) {
        // Deal 1 damage. Deal 1 additional damage for each time this spell was cast previously this turn
        $this->dealDamage(1 + Globals::getAmnesiaCount());
        Globals::incAmnesiaCount();
    }
}
