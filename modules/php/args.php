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
            "last_basic_attack_damage" => Globals::getLastBasicAttackDamage(),
        ];
    }

    function argActivateDelayedSpell() {
        $args = $this->getArgsBase();
        $args["spells"] = array_values(Globals::getCoolDownDelayedSpellIds(true));
        return $args;
    }

    function argCastSpell() {
        $args = $this->getArgsBase();
        $args["discount_attack_spell"] = Globals::getDiscountAttackSpell(true);
        $args["discount_next_spell"] = Globals::getDiscountNextSpell(true);
        $args["previous_spell_played"] = Globals::getSpellPlayed();
        $args["previous_spell_cost"] = Globals::getSpellCost();
        $args["undo"] = Game::get()->getGameStateValue(WG_VAR_UNDO_AVAILABLE) == 1;
        return $args;
    }

    function argCastSpellInteraction() {
        $args = $this->getArgsBase();
        $args["spell"] = SpellCard::get(Globals::getSpellPlayed());
        $args["previous_spell_played"] = Globals::getPreviousSpellPlayed();
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
        $args["undo"] = Game::get()->getGameStateValue(WG_VAR_UNDO_AVAILABLE) == 1;
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
                "name" => "lullaby",
                "active" => Globals::getIsActiveLullaby(),
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

        $first_player = Players::getPlayerId();
        $second_player = Players::getOpponentIdOf($first_player);

        $result = [
            'ongoing_spells' => array_values($ongoing_spell),
            'players' => [
                $first_player => Game::get()->getStat(WG_STAT_TURN_NUMBER, $first_player),
                $second_player => Game::get()->getStat(WG_STAT_TURN_NUMBER, $second_player),
            ],
            'last_added_spell' => Globals::getLastAddedSpell(),
        ];

        return $result;
    }
}
