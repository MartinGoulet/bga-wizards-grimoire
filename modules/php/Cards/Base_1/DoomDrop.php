<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Cards\BaseCard;
class DoomDrop extends BaseCard {

    public function castSpell($args) {
        // Each time a mana card is discarded of this spell, deal damage equal to that mana's power
        $mana_card = $args;

        $this->dealDamage(intval($mana_card['type']));
    }
}
