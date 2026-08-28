<?php

namespace WizardsGrimoire\Cards\Base_2;

use WizardsGrimoire\Cards\OngoingBaseCard;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;

class BattleVision extends OngoingBaseCard {

    public function isActive(): bool {
        return $this->isActiveAtLeastOneMana() 
            && SpellCard::isInRepertoireBool($this->id, Players::getOpponentId());
    }

    public function getArguments(): array {
        return [
            'name' => 'battlevision',
            'active' => $this->isActive(),
        ];
    }
}
