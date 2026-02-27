<?php

namespace WizardsGrimoireExt\Cards\Base_2;

use WizardsGrimoireExt\Cards\OngoingBaseCard;
use WizardsGrimoireExt\Core\Players;
use WizardsGrimoireExt\Core\SpellCard;

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
