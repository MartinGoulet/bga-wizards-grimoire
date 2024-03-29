<?php

namespace WizardsGrimoire\Cards\KickStarter_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Globals;

class FalseFace extends BaseCard {

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
