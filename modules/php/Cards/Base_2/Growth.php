<?php

namespace WizardsGrimoire\Cards\Base_2;

use WizardsGrimoire\Cards\OngoingBaseCard;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;

class Growth extends OngoingBaseCard {

    public function isActive(): bool {
        return $this->isActiveAtLeastOneMana() 
            && SpellCard::isInRepertoireBool($this->id, Players::getPlayerId());
    }

    public function getArguments(): array {
        return [
            'name' => 'growth',
            'active' => $this->isActive(),
        ];
    }

}
