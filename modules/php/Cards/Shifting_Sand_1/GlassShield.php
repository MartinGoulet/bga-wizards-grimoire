<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use WizardsGrimoireExt\Cards\OngoingBaseCard;
use WizardsGrimoireExt\Core\Players;

class GlassShield extends OngoingBaseCard {

    public function isActive(): bool {
        return $this->isActiveAtLeastOneMana()
            && $this->getOwnerId() == Players::getPlayerId();
    }

    public function getArguments(): array {
        return [
            'name' => 'glassshield',
            'active' => $this->isActive(),
        ];
    }

}