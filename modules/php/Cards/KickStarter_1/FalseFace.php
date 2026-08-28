<?php

namespace WizardsGrimoire\Cards\KickStarter_1;

use WizardsGrimoire\Cards\OngoingBaseCard;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;

class FalseFace extends OngoingBaseCard {

    public function isActive(): bool {
        return $this->isActiveAtLeastOneMana()
            && SpellCard::isInRepertoireBool($this->id, Players::getPlayerId());
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
