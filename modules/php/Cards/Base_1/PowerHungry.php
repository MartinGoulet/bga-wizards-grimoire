<?php

namespace WizardsGrimoireExt\Cards\Base_1;

use WizardsGrimoireExt\Cards\OngoingBaseCard;

class PowerHungry extends OngoingBaseCard {

    public function isActive(): bool {
        return $this->isActiveAtLeastOneMana();
    }

    public function getArguments(): array {
        return [
            'name' => 'powerhungry',
            'active' => $this->isActive(),
        ];
    }
    
}
