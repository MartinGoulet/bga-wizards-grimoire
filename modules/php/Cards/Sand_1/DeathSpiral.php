<?php

namespace WizardsGrimoireExt\Cards\Sand_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\Globals;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\SpellCard;

class DeathSpiral extends BaseCard {

    public function castSpell($args) {
        // This spell costs 0 if the previous spell you cast cost 3 or more. Deal 2 damage
        $this->dealDamage(2);
    }

    public function discount() {
        $spell = SpellCard::get(Globals::getSpellPlayed());
        if ($spell == null) {
            Notifications::spellNoEffect();
            return;
        }

        $cost = Globals::getSpellCost();
        return $cost >= 3 ? 99 : 0;
    }
}
