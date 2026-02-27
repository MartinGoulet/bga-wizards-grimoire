<?php

namespace WizardsGrimoireExt\Cards\KickStarter_1;

use WizardsGrimoireExt\Cards\OngoingBaseCard;
use WizardsGrimoireExt\Core\Globals;
use WizardsGrimoireExt\Core\Players;

class FalseFace extends OngoingBaseCard {

    public function isActive(): bool {
        return $this->isActiveAtLeastOneMana()
            && $this->getOwnerId() == Players::getPlayerId();
    }

    public function getArguments(): array {
        return [
            'name' => 'falseface',
            'active' => $this->isActive(),
        ];
    }

    // If you deal 1 or less damage during your basic attack phase, deal 3 damage when your turn ends
    public function onTurnEnd() {
        if (Globals::getLastBasicAttackDamage() <= 1) {
            $this->dealDamage(3);
        }
    }

    protected function getCardName() {
        return $this->getCardNameFromType();
    }
}
