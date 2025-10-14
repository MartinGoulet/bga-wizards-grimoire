<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use WizardsGrimoireExt\Cards\BaseCard;

class InfiniteFlame extends BaseCard {

    public function onModifyBasicAttackDamage(int $damage): int {
        return $damage + 1;
    }

}