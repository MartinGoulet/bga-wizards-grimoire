<?php

namespace WizardsGrimoireExt\Cards\Sand_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\Globals;
use WizardsGrimoireExt\Core\Notifications;

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
