<?php

namespace WizardsGrimoire\Cards\Base_2;

use WizardsGrimoire\Cards\BaseCard;

class RecklessAttack extends BaseCard {

    public function castSpell($args) {
        // Deal 1 damage each time you discard off this spell. If you discard a 4 power mana off this spell, deal 4 damage instead
        $value = intval($args['type']);
        $this->dealDamage($value == 4 ? 4 : 1);
    }
}
