<?php

namespace WizardsGrimoire\Core;

use Bga\Games\WizardsGrimoire\Game;
use WizardsGrimoire\Cards\Base_2\Growth;
use WizardsGrimoire\Cards\Base_2\Puppetmaster;
use WizardsGrimoire\Cards\OngoingBaseCard;
use WizardsGrimoire\Cards\Shifting_Sand_1\Blossom;

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
        $args["spells"] = array_values(Globals::getCoolDownDelayedSpellIds());
        return $args;
    }

    function argCastSpell() {
        $args = $this->getArgsBase();
        $args["discount_attack_spell"] = Globals::getDiscountAttackSpell();
        $args["discount_next_spell"] = Globals::getDiscountNextSpell();
        $args["previous_spell_played"] = Globals::getSpellPlayed();
        $args["previous_spell_cost"] = Globals::getSpellCost();
        $args["cursed_mind"] = Globals::getCursedMindIncreaseCost();
        $args["time_walk"] = Globals::getTimeWalkDecreaseCost();
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

        /** @var Puppetmaster $pupperMaster */
        $pupperMaster = SpellCard::getInstanceOfCardFromClass(Puppetmaster::class);
        if ($pupperMaster !== null && $pupperMaster->isActive()) {
            $value = Globals::getPreviousBasicAttackPower();
            $cards = array_filter($cards, function ($card) use ($value) {
                return ManaCard::getPower($card) == $value;
            });
        }
        $isActiveGlassShield = SpellCard::isActiveGlassShield(Players::getPlayerId());
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

    public function getArgsBase() {
        $names = ['secretoath', 'growth', 'falseface', 'lullaby', 'battlevision', 'powerhungry', 'puppetmaster', 
                  'infiniteflame', 'glassshield', 'feverdream', 'multiply', 'blossom', 'sunkenskull'];

        // Initialize associative array keyed by name, all inactive
        $ongoing_spell = [];
        foreach ($names as $n) {
            $ongoing_spell[$n] = ['name' => $n, 'active' => false];
        }

        $cards = array_merge(
            SpellCard::getOngoingSpells(Players::getPlayerId()),
            SpellCard::getOngoingSpells(Players::getOpponentId())
        );

        foreach ($cards as $spell) {
            /** @var \WizardsGrimoire\Cards\OngoingBaseCard $instance */
            $instance = SpellCard::getInstanceOfCard($spell);
            $info = $instance->getArguments();
            $ongoing_spell[$info['name']] = $info;
        }

        $ongoing_spell['sunkenskull'] = [
            'name' => 'sunkenskull',
            'active' => Globals::getSunkenSkullActivePlayer() == Players::getPlayerId(),
        ];

        $first_player = Players::getPlayerId();
        $second_player = Players::getOpponentIdOf($first_player);

        $result = [
            'ongoing_spells' => array_values($ongoing_spell),
            'players' => [
                $first_player => Game::get()->bga->playerStats->get(WG_STAT_TURN_NUMBER, $first_player),
                $second_player => Game::get()->bga->playerStats->get(WG_STAT_TURN_NUMBER, $second_player),
            ],
            'last_added_spell' => Globals::getLastAddedSpell(),
        ];

        return $result;
    }
}
