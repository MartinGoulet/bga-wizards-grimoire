<?php

namespace WizardsGrimoire\Cards\Sand_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\SpellCard;

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

        $cost = intval(SpellCard::getCardInfo($spell)['cost']);
        return $cost >= 3 ? 99 : 0;
    }
}
