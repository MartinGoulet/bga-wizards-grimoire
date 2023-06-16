<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Core\ManaCard;

class TouchTheVoid extends BaseCard {

    public function castSpell($args) {
        // Deal damage equal to the quantity of mana cards on 1 of your spells
        $max_value = 0;

        for ($i = 1; $i <= 6; $i++) {
            $count = ManaCard::countOnTopOfManaCoolDown($i);
            if ($count > $max_value) {
                $max_value = $count;
            }
        }

        $this->dealDamage($max_value);
    }
}
