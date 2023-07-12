<?php

namespace WizardsGrimoire\Cards\Base_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;

class MirrorImage extends BaseCard {

    public function castSpell($args) {
        // Gain mana equal to the quantity of mana cards on 1 of your spells
        $position = intval(array_shift($args));
        $mana_cooldown_count = ManaCard::countOnTopOfManaCoolDown($position);
        $this->drawManaCards($mana_cooldown_count);
    }
}
