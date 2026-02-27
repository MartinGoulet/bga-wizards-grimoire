<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use WizardsGrimoireExt\Cards\OngoingBaseCard;
use WizardsGrimoireExt\Core\Players;

class Blossom extends OngoingBaseCard {

    public function isActive(): bool {
        return $this->isActiveExactMana(3)
            && $this->getOwnerId() == Players::getPlayerId();
    }

    public function getArguments(): array {
        return [
            'name' => 'blossom',
            'active' => $this->isActive(),
        ];
    }

    public function onModifyManaPower(int $power): int {
        $isActive = $this->isActive();
        return $isActive ? $power + 2 : $power;
    }

}