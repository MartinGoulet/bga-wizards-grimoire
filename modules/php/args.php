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

    function argPlayerNewTurn() {
        return [
            "previous_basic_attack" => Globals::getPreviousBasicAttackPower(),
        ];
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

    function argBattleVision() {
        $args = $this->getArgsBase();
        $card = ManaCard::getBasicAttack();
        $args["value"] = intval($card['type']);
        return $args;
    }

    function argBasicAttack() {
        $cards = ManaCard::getHand();
        if (Globals::getIsActivePuppetmaster()) {
            $value = Globals::getPreviousBasicAttackPower();
            $cards = array_filter($cards, function ($card) use ($value) {
                return ManaCard::getPower($card) == $value;
            });
        }
        $args = $this->getArgsBase();
        $args['_private'] = [
            'active' => [
                'allowed_manas' => array_values($cards),
            ],
        ];
        // $args["allowed_manas"] = array_values($cards);
        return $args;
    }

    //////////////////////////////////////////
    // Private methods

    private function getArgsBase() {
        $ongoing_spell = [
            [
                "name" => "battlevision",
                "active" => Globals::getIsActiveBattleVision(),
            ],
            [
                "name" => "growth",
                "active" => Globals::getIsActiveGrowth(),
            ],
            [
                "name" => "puppetmaster",
                "active" => Globals::getIsActivePuppetmaster(),
            ],
            [
                "name" => "powerhungry",
                "active" => Globals::getIsActivePowerHungry(),
            ],
            [
                "name" => "secretoath",
                "active" => Globals::getIsActiveSecretOath(),
            ]
        ];

        $result = ['ongoing_spells' => array_values($ongoing_spell)];

        return $result;
    }
}
