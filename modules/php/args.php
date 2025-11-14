<?php

namespace WizardsGrimoireExt\Core;

use Bga\Games\wizardsgrimoireext\Game;
use WizardsGrimoireExt\Cards\Shifting_Sand_1\Blossom;

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
        $args["discount_attack_spell"] = Globals::getDiscountAttackSpell();
        $args["discount_next_spell"] = Globals::getDiscountNextSpell();
        $args["previous_spell_played"] = Globals::getSpellPlayed();
        $args["previous_spell_cost"] = Globals::getSpellCost();
        $args["cursed_mind"] = Globals::getCursedMindIncreaseCost();
        $args["crescendo"] = Globals::getCrescendoIncreaseCost();
        $args['premonition_discount'] = Globals::getDiscountPremonition();
        $args["undo"] = Game::get()->getGameStateValue(WG_VAR_UNDO_AVAILABLE) == 1;

        $args['spell_discount'] = $this->getSpellsDiscount();

        return $args;
    }

    public function getSpellsDiscount() : array {
        $spells = SpellCard::getCardsFromRepertoire();
        $spellManaDiscount = [];

        foreach ($spells as $spell) {
            $instance = SpellCard::getInstanceOfCard($spell);
            if (!method_exists($instance, 'getSpellDiscount')) {
                continue;
            }
            $spellManaDiscount[intval($spell['id'])] = $instance->getSpellDiscount();
        }

        return $spellManaDiscount;
    }

    function argCastSpellInteraction() {
        $args = $this->getArgsBase();
        $args["spell"] = SpellCard::get(Globals::getSpellPlayed());
        $args["previous_spell_played"] = Globals::getPreviousSpellPlayed();

        $instance = SpellCard::getInstanceOfCard($args["spell"]);
        if (method_exists($instance, 'getCastSpellInteractionArgs')) {
            $interactionArgs = $instance->getCastSpellInteractionArgs();
            $args = array_merge($args, $interactionArgs);
        }
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
        $isActiveGlassShield = SpellCard::isActiveGlassShield(Players::getOpponentId());
        if ($isActiveGlassShield) {
            $powers = [];
            foreach ($cards as $card) {
                $power = ManaCard::getPower($card);
                if (!isset($powers[$power])) {
                    $powers[$power] = [];
                }
                $powers[$power][] = $card;
            }
            $cardTemps = [];
            foreach ($powers as $powerGroup) {
                if (count($powerGroup) > 1) {
                    $cardTemps = array_merge($cardTemps, $powerGroup);
                }
            }
            $cards = $cardTemps;
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
            ],
            [
                "name" => "sunkenskull",
                "active" => Globals::getSunkenSkullActivePlayer() == Players::getPlayerId(),
            ]
        ];

        $ongoingSpellActive = SpellCard::getOngoingSpells(Players::getPlayerId());
        foreach ($ongoingSpellActive as $spell) {
            $instance = SpellCard::getInstanceOfCard($spell);
            if ($instance instanceof \WizardsGrimoireExt\Cards\Shifting_Sand_1\Blossom) {
                $ongoing_spell[] = [
                    "name" => "blossom",
                    "active" => $instance->isActive(),
                ];
            }
        }

        $opponentSpellActive = SpellCard::getOngoingSpells(Players::getOpponentId());

        $opponentSpellActive = array_filter($opponentSpellActive, function ($spell) {
            /** @var \WizardsGrimoireExt\Cards\OngoingBaseCard $instance */
            $instance = SpellCard::getInstanceOfCard($spell);
            if($instance instanceof \WizardsGrimoireExt\Cards\OngoingBaseCard) {
                return $instance->isActive();
            }
            return false;
        });

        foreach ($opponentSpellActive as $spell) {
            $instance = SpellCard::getInstanceOfCard($spell);
            if ($instance instanceof \WizardsGrimoireExt\Cards\Shifting_Sand_1\GlassShield) {
                $ongoing_spell[] = [
                    "name" => "glassshield",
                    "active" => $instance->isActive(),
                ];
            }
        }

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
