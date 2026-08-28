<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_1;

use WizardsGrimoire\Cards\OngoingBaseCard;

class FeverDream extends OngoingBaseCard {

    public function isActive(): bool {
        return $this->isActiveAtLeastOneMana();
    }

    public function getArguments(): array {
        return [
            'name' => 'feverdream',
            'active' => $this->isActive(),
        ];
    }

    public function onAddSpellToRepertoire(array $card) {
        $this->dealDamage(2);
    }

}