<?php

namespace WizardsGrimoireExt\Cards\Base_2;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;

class IceStorm extends BaseCard {

    public function castSpell($args) {
        // Deal 1 damage for each of your spells with exactly 1 mana card on them.
        $total = 0;
        for ($i = 1; $i <= 6; $i++) {
            $count = ManaCard::countOnTopOfManaCoolDown($i);
            if ($count == 1) {
                $total++;
            }
        }

        $this->dealDamage($total);
    }
}
