<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Cards\OngoingBaseCard;

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
