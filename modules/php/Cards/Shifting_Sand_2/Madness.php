<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_2;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\Globals;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\SpellCard;

class Madness extends BaseCard {

    public function castSpell($args) {
        // This spell costs 2 less if the previous spell you cast cost 3 or more. Deal 2 damage
        $this->dealDamage(2);
    }

    public function getSpellDiscount() {

        // This spell costs 2 less if the previous spell you cast cost 3 or more.
        $spellIds = Globals::getPlayedSpellsThisTurn();

        if (intval(current($spellIds)) == $this->id) {
            return 0;
        } else {
            $previousSpellId = intval(current($spellIds));
        }

        $previousSpell = SpellCard::get($previousSpellId);
        $cost = SpellCard::getCardInfo($previousSpell)['cost'];
        // var_dump($cost);
        return $cost >= 3 ? 2 : 0;
    }
}
