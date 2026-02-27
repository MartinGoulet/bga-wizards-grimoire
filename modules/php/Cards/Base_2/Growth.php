<?php

namespace WizardsGrimoireExt\Cards\Base_2;

use WizardsGrimoireExt\Cards\OngoingBaseCard;
use WizardsGrimoireExt\Core\Players;
use WizardsGrimoireExt\Core\SpellCard;

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
