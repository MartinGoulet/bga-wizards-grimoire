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

    function argBase() {
        return $this->getArgsBase();
    }

    function argActivateDelayedSpell() {
        $args = $this->getArgsBase();
        $args["spells"] = Globals::getCoolDownDelayedSpellIds(true);
        return $args;
    }

    function argCastSpell() {
        $args = $this->getArgsBase();
        $args["discount_attack_spell"] = Globals::getDiscountAttackSpell(true);
        $args["discount_next_spell"] = Globals::getDiscountNextSpell(true);
        return $args;
    }

    function argCastSpellInteraction() {
        $args = $this->getArgsBase();
        $args["spell"] = SpellCard::get(Globals::getSpellPlayed());
        return $args;
    }



    //////////////////////////////////////////
    // Private methods

    private function getArgsBase() {
        $result = [];
        $result['ongoing_spells'] = [];
        $result['ongoing_spells'][] = [
            "name" => WG_ONGOING_SPELL_ACTIVE_GROWTH, "active" => Globals::getIsActiveGrowth()
        ];

        return $result;
    }
}
