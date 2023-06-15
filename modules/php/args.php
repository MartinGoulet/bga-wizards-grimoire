<?php

namespace WizardsGrimoire\Core;

trait ArgsTrait {
    
//////////////////////////////////////////////////////////////////////////////
//////////// Game state arguments
////////////

    /*
        Here, you can create methods defined as "game state arguments" (see "args" property in states.inc.php).
        These methods function is to return some additional information that is specific to the current
        game state.
    */

    function argActivateDelayedSpell() {
        return [
            "spells" => Globals::getCoolDownDelayedSpell(true)
        ];
    }

    function argCastSpell() {
        return [
            "discount_attack_spell" => Globals::getDiscountAttackSpell(true),
            "discount_next_spell" => Globals::getDiscountNextSpell(true),
        ];
    }

    function argCastSpellInteraction() {
        return [
            "spell" => SpellCard::get(Globals::getSpellPlayed()),
        ];
    }

}