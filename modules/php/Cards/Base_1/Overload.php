<?php

namespace WizardsGrimoireExt\Cards\Base_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Players;

class Overload extends BaseCard {

    public function castSpell($args) {
        // Deal damage equal to the quantity of mana cards on 1 of your opponent's spell
        $opponent_id = Players::getOpponentId();
        $max_value = 0;

        for ($i = 1; $i <= 6; $i++) {
            $count = ManaCard::countOnTopOfManaCoolDown($i, $opponent_id);
            if ($count > $max_value) {
                $max_value = $count;
            }
        }

        $this->dealDamage($max_value);
    }
}
