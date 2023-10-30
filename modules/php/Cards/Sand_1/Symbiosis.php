<?php

namespace WizardsGrimoire\Cards\Sand_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\Notifications;

class Symbiosis extends BaseCard {

    public function castSpell($args) {
        // Gain a quantity of mana cards equal to the damage dealt by your opponent's previous basic attack
        $dmg = Globals::getLastBasicAttackDamage();
        if ($dmg == 0) {
            Notifications::spellNoEffect();
        } else {
            $this->drawManaCards($dmg);
        }
    }
}
