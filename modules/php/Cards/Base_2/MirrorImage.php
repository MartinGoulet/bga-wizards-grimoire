<?php

namespace WizardsGrimoire\Cards\Base_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;

class MirrorImage extends BaseCard {

    public function castSpell($args) {
        // Gain mana equal to the quantity of mana cards on 1 of your spells
        $max_count = 0;
        for ($i = 1; $i <= 6; $i++) {
            $mana_cooldown_count = ManaCard::countOnTopOfManaCoolDown($i);
            $max_count = max($max_count, $mana_cooldown_count);
        }

        $this->drawManaCards($max_count);
    }
}
