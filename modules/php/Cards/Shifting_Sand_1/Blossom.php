<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use WizardsGrimoireExt\Cards\OngoingBaseCard;

class Blossom extends OngoingBaseCard {

    public function isActive(): bool {
        return $this->isActiveExactMana(3);
    }

    public function onModifyManaPower(int $power): int {
        $isActive = self::isActive();
        return $isActive ? $power + 2 : $power;
    }

}