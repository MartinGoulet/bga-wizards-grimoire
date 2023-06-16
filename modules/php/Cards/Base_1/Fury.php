<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Players;

class Fury extends BaseCard {

    public function castSpell($args) {
        // Deal 1 damage for each of your opponent's spells that have mana on them
        $opponent_id = Players::getOpponentId();

        $total = 0;
        for ($i = 1; $i <= 6; $i++) {
            $count = ManaCard::countOnTopOfManaCoolDown($i, $opponent_id);
            if ($count > 0) {
                $total++;
            }
        }

        $this->dealDamage($total);
    }
}
