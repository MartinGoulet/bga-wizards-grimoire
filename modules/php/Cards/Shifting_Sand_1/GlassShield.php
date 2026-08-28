<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_1;

use WizardsGrimoire\Cards\OngoingBaseCard;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;

class GlassShield extends OngoingBaseCard {

    public function isActive(): bool {
        return $this->isActiveAtLeastOneMana()
            && SpellCard::isInRepertoireBool($this->id, Players::getOpponentId());
    }

    public function getArguments(): array {
        return [
            'name' => 'glassshield',
            'active' => $this->isActive(),
        ];
    }

}