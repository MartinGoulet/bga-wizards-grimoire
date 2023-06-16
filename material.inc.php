<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * WizardsGrimoire implementation : © Martin Goulet <martin.goulet@live.ca>
 * 
 * This code has been produced on the BGA studio platform for use on http://boardgamearena.com.
 * See http://en.boardgamearena.com/#!doc/Studio for more information.
 * -----
 *
 * material.inc.php
 *
 * WizardsGrimoire game material description
 *
 * Here, you can describe the material of your game with PHP variables.
 *   
 * This file is loaded in your game logic class constructor, ie these variables
 * are available everywhere in your game logic code.
 *
 */


$this->card_types = [
  1 => [
    "name" => clienttranslate("Touch the void"),
    "class" => "TouchTheVoid",
    "description" => clienttranslate("Deal damage equal to the quantity of mana cards on 1 of your spells"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_BASE_1,
    "debug" => "lightgreen",
  ],
  2 => [
    "name" => clienttranslate("Overload"),
    "class" => "Overload",
    "description" => clienttranslate("Deal damage equal to the quantity of mana cards on 1 of your opponent's spell"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_BASE_1,
    "debug" => "lightgreen",
  ],
  3 => [
    "name" => clienttranslate("Mutation"),
    "class" => "Mutation",
    "description" => clienttranslate("Gain 1 mana card. Deal damage equal to that mana's power"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_BASE_1,
    "debug" => "lightgreen",
  ],
  4 => [
    "name" => clienttranslate("Stone crush"),
    "class" => "StoneCrush",
    "description" => clienttranslate("Deal 1 damage for each mana card in your opponent's hand"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_BASE_1,
    "debug" => "lightgreen",
  ],
  5 => [
    "name" => clienttranslate("Mist of pain"),
    "class" => "MistOfPain",
    "description" => clienttranslate("Your opponent may discard up to 4 mana cards from their hand. For each mana they do not discard, deal 1 damage"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_BASE_1,
    "interaction" => "opponent",
    "js_actions_interaction" => "actionMistOfPain",
    "debug" => "lightgreen",
  ],
  6 => [
    "name" => clienttranslate("Arcane tactics"),
    "class" => "ArcaneTactics",
    "description" => clienttranslate("Gain 7 mana cards from the mana deck. Then place 4 mana cards from your hand on top of the mana deck in any order"),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_BASE_1,
    "interaction" => "player",
    "js_actions_interaction" => "actionArcaneTactics",
    "debug" => "lightgreen",
  ],
  7 => [
    "name" => clienttranslate("Freeze"),
    "class" => "Freeze",
    "description" => clienttranslate("Choose 1:  Gain 4 mana cards, or place a mana card from the mana deck on one of your opponent's spells"),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_BASE_1,
    "js_actions" => "actionFreeze",
    "debug" => "lightgreen",
  ],
  8 => [
    "name" => clienttranslate("Energy reserve"),
    "class" => "EnergyReserve",
    "description" => clienttranslate("Pick up a mana card off 1 of your others spells. Gain mana equal to that mana's power."),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_BASE_1,
    "js_actions" => "actionSelectManaFrom",
    "debug" => "lightgreen",
  ],
  9 => [
    "name" => clienttranslate("Fracture"),
    "class" => "Fracture",
    "description" => clienttranslate("Gain 4 mana cards. You may move a mana card between 2 of your other spells"),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_BASE_1,
    "debug" => "lightgreen",
    "js_actions" => ["actionSelectManaFrom", "actionSelectManaTo"],
  ],
  10 => [
    "name" => clienttranslate("Gleam of hope"),
    "class" => "GleamOfHope",
    "description" => clienttranslate("Gain mana until you have 5 mana cards in your hand"),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_BASE_1,
    "debug" => "lightgreen",
  ],

  11 => [
    "name" => clienttranslate("Rejuvenation"),
    "class" => "Rejuvenation",
    "description" => clienttranslate("Choose 1: Gain 4 mana cards, or take 2 mana cards of any power from the discard pile"),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_BASE_1,
    "js_actions" => "actionRejuvenation",
    "debug" => "lightgreen",
  ],
  12 => [
    "name" => clienttranslate("Shackled motion"),
    "class" => "ShackledMotion",
    "description" => clienttranslate("Choose 1: Gain 4 mana cards, or your opponent must discard their hand"),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_BASE_1,
    "js_actions" => "actionShackledMotion",
    "debug" => "lightgreen",
  ],
  13 => [
    "name" => clienttranslate("Living wind"),
    "class" => "LivingWind",
    "description" => clienttranslate("Gain 6 mana cards, gain 1 fewer mana for each of your other spells that have a mana on them"),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_BASE_1,
    "debug" => "lightgreen",
  ],
  14 => [
    "name" => clienttranslate("Coercive agreement"),
    "class" => "CoerciveAgreement",
    "description" => clienttranslate("Choose 1: Take up to 3 randomly selected mana from your opponent's hand, or discard a mana card off 2 of your other spells."),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_BASE_2,
    "debug" => "red",
  ],

  15 => [
    "name" => clienttranslate("Arcane eye"),
    "class" => "ArcaneEye",
    "description" => clienttranslate("Pick up a mana card of each of your spells that costs 3 or more"),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_BASE_2,
    "debug" => "yellow",
  ],
  16 => [
    "name" => clienttranslate("Haste"),
    "class" => "Haste",
    "description" => clienttranslate("The next time you cast a spell this turn, it costs 2 less."),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_BASE_1,
    "debug" => "lightgreen",
  ],
  17 => [
    "name" => clienttranslate("Renewed fervor"),
    "class" => "RenewedFervor",
    "description" => clienttranslate("Pick up a mana card off each of your instant attack spells that costs 2 or less."),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_BASE_1,
    "debug" => "lightgreen",
  ],
  18 => [
    "name" => clienttranslate("Secret Oath"),
    "class" => "SecretOath",
    "description" => clienttranslate("As long as this spell has mana on it, if your opponent has a 4 power mana in their hand, they must give it to you immediately"),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_ONGOING,
    "cost" => 2,
    "icon" => WG_ICON_SET_BASE_2,
    "debug" => "red",
  ],
  19 => [
    "name" => clienttranslate("Time distortion"),
    "class" => "TimeDistortion",
    "description" => clienttranslate("Pick up a mana card of 2 of your other spells"),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_BASE_1,
    "debug" => "lightgreen",
    "js_actions" => "actionTimeDistortion",
  ],
  20 => [
    "name" => clienttranslate("Betrayal"),
    "class" => "Betrayal",
    "description" => clienttranslate("Deal 5 damage. Your opponent gains 3 mana cards."),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 3,
    "icon" => WG_ICON_SET_BASE_1,
    "debug" => "lightgreen",
  ],

  21 => [
    "name" => clienttranslate("Blood lust"),
    "class" => "BloodLust",
    "description" => clienttranslate("Deal 1 damage. Deal an additionnal 2 damage for each attack spell cast consecutively before this spell"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 3,
    "icon" => WG_ICON_SET_BASE_2,
    "debug" => "red",
  ],
  22 => [
    "name" => clienttranslate("Delusion"),
    "class" => "Delusion",
    "description" => clienttranslate("Deal 3 damage. You may pick up a mana card off 1 of your opponent's spells"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 3,
    "icon" => WG_ICON_SET_BASE_2,
    "debug" => "yellow",
  ],
  23 => [
    "name" => clienttranslate("Vile laughter"),
    "class" => "VileLaughter",
    "description" => clienttranslate("Deal 6 damage, minus the highest power mana in your hand"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 3,
    "icon" => WG_ICON_SET_BASE_1,
    "debug" => "lightgreen",
  ],
  24 => [
    "name" => clienttranslate("Doom drop"),
    "class" => "DoomDrop",
    "description" => clienttranslate("Each time a mana card is discarded of this spell, deal damage equal to that mana's power"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_DELAYED,
    "activation_auto" => true,
    "cost" => 3,
    "icon" => WG_ICON_SET_BASE_1,
    "debug" => "lightgreen",
  ],

  25 => [
    "name" => clienttranslate("Energy storm"),
    "class" => "EnergyStorm",
    "description" => clienttranslate("Deal 3 damage. Deal 1 additional damage for each 4 power mana in your hand"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 3,
    "icon" => WG_ICON_SET_BASE_1,
    "debug" => "lightgreen",
  ],
  26 => [
    "name" => clienttranslate("Fire blast"),
    "class" => "FireBlast",
    "description" => clienttranslate("Discard all mana in your hand. Deal 7 damage"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 3,
    "icon" => WG_ICON_SET_BASE_1,
    "debug" => "lightgreen",
  ],
  27 => [
    "name" => clienttranslate("Painful vision"),
    "class" => "PainfulVision",
    "description" => clienttranslate("Deal 5 damage. Place a mana from the mana deck on all your spells with 1 mana card on them"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 3,
    "icon" => WG_ICON_SET_BASE_2,
    "debug" => "red",
  ],
  28 => [
    "name" => clienttranslate("Possessed"),
    "class" => "Possessed",
    "description" => clienttranslate("Deal 5 damage. Your opponent may give you a mana from their hand. If they do, reduce your damage by the mana's power"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 3,
    "icon" => WG_ICON_SET_BASE_2,
    "debug" => "red",
  ],
  29 => [
    "name" => clienttranslate("Second strike"),
    "class" => "SecondStrike",
    "description" => clienttranslate("Deal 3 damage. The next time you cast an attack spell this turn, it costs 1 less."),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 3,
    "icon" => WG_ICON_SET_BASE_1,
    "debug" => "red",
  ],
  30 => [
    "name" => clienttranslate("Trap attack"),
    "class" => "TrapAttack",
    "description" => clienttranslate("Pick up a mana card off 1 of your other spells. Deal damage equal to that mana's power"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 3,
    "icon" => WG_ICON_SET_BASE_1,
    "js_actions" => "actionTrapAttack",
    "debug" => "lightgreen",
  ],
  31 => [
    "name" => clienttranslate("Energy shield"),
    "class" => "EnergyShield",
    "description" => clienttranslate("Pick up a mana card off 1 of your other spells. Place a mana card from the mana deck on 1 of your opponent's spells."),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 3,
    "icon" => WG_ICON_SET_BASE_2,
    "debug" => "red",
  ],
  32 => [
    "name" => clienttranslate("Growth"),
    "class" => "Growth",
    "description" => clienttranslate("During your turn, increase the power of all mana by 1"),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_ONGOING,
    "cost" => 3,
    "icon" => WG_ICON_SET_BASE_2,
    "debug" => "red",
  ],
  33 => [
    "name" => clienttranslate("Fury"),
    "class" => "Fury",
    "description" => clienttranslate("Deal 1 damage for each of your opponent's spells that have mana on them"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 4,
    "icon" => WG_ICON_SET_BASE_1,
    "debug" => "lightgreen",
  ],
  34 => [
    "name" => clienttranslate("Hellstorm"),
    "class" => "Hellstorm",
    "description" => clienttranslate("Gain 5 mana cards. Deal damage equal to the highest power mana you gain"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 4,
    "icon" => WG_ICON_SET_BASE_1,
    "debug" => "lightgreen",
  ],


  35 => [
    "name" => clienttranslate("Shadow attack"),
    "class" => "ShadowAttack",
    "description" => clienttranslate("Discard a mana card off 1 of your other spells. Deal damage and gain mana equal to that mana's power"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 4,
    "icon" => WG_ICON_SET_BASE_1,
    "debug" => "lightgreen",
    "js_actions" => "actionSelectManaFrom",
  ],
  36 => [
    "name" => clienttranslate("Crushing blow"),
    "class" => "CrushingBlow",
    "description" => clienttranslate("Deal 6 damage"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 5,
    "icon" => WG_ICON_SET_BASE_1,
    "debug" => "lightgreen",
  ],
  37 => [
    "name" => clienttranslate("False face"),
    "class" => "FalseFace",
    "description" => clienttranslate("If you deal 1 or less damage during your basic attack phase, deal 3 damage when your turn ends"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_ONGOING,
    "cost" => 1,
    "icon" => WG_ICON_SET_KICKSTARTER_1,
    "debug" => "red",
  ],
  38 => [
    "name" => clienttranslate("Quick swap"),
    "class" => "QuickSwap",
    "description" => clienttranslate("Each time you discard off this spell, choose 1: deal 1 damage, or discard this spell and replace it with a new spell"),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_DELAYED,
    "activation_auto" => false,
    "cost" => 1,
    "icon" => WG_ICON_SET_KICKSTARTER_1,
    "debug" => "red",
  ],

  39 => [
    "name" => clienttranslate("Lullaby"),
    "class" => "Lullaby",
    "description" => clienttranslate("As long as there is mana on this spell, if you have 0 mana cards in your hand, gain 2 mana cards"),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_ONGOING,
    "cost" => 1,
    "icon" => WG_ICON_SET_KICKSTARTER_1,
    "debug" => "red",
  ],
  40 => [
    "name" => clienttranslate("Amnesia"),
    "class" => "Amnesia",
    "description" => clienttranslate("Deal 1 damage. Deal 1 additional damage for each time this spell was cast previously this turn"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 1,
    "icon" => WG_ICON_SET_KICKSTARTER_1,
    "debug" => "red",
  ],

  41 => [
    "name" => clienttranslate("Hoodwink"),
    "class" => "Hoodwink",
    "description" => clienttranslate("Deal 7 damage, minus the damage your opponent dealt their last basic attack phase"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 3,
    "icon" => WG_ICON_SET_KICKSTARTER_1,
    "debug" => "red",
  ],
  42 => [
    "name" => clienttranslate("Wild bloom"),
    "class" => "WildBloom",
    "description" => clienttranslate("When you discard the last mana off this spell, you may immediately cast an instant spell of yours that has 0 mana on it for no cost"),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_DELAYED,
    "activation_auto" => false,
    "cost" => 2,
    "icon" => WG_ICON_SET_KICKSTARTER_1,
    "debug" => "red",
  ],
  43 => [
    "name" => clienttranslate("Tsunami"),
    "class" => "Tsunami",
    "description" => clienttranslate("Gain 1 mana card. Deal 1 damage for each mana of a unique power in your hand"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_KICKSTARTER_1,
    "debug" => "yellow",
  ],
  44 => [
    "name" => clienttranslate("Twist of fate"),
    "class" => "TwistOfFate",
    "description" => clienttranslate("Deal 2 damage. You may discard and replace 1 of your other spells with a new spell. Move all mana on it onto the new spell"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 3,
    "icon" => WG_ICON_SET_KICKSTARTER_1,
    "debug" => "red",
  ],


  45 => [
    "name" => clienttranslate("Fatal flaw"),
    "class" => "FatalFlaw",
    "description" => clienttranslate("Deal 1 damage. Reveal the top mana card on 1 of your opponents spells, dealing additional damage equal to it's power."),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_KICKSTARTER_1,
    "debug" => "red",
  ],
  46 => [
    "name" => clienttranslate("Affliction"),
    "class" => "Affliction",
    "description" => clienttranslate("Gain 4 mana cards. You may deal 1 damage to yourself. If you do, gain 2 extra cards"),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_KICKSTARTER_1,
    "debug" => "red",
  ],
  47 => [
    "name" => clienttranslate("Friendly truce"),
    "class" => "FriendlyTruce",
    "description" => clienttranslate("Your opponent may give you 3 cards from their hand. If they do not, gain 5 mana cards"),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_BASE_1,
    "interaction" => "opponent",
    "js_actions_interaction" => "actionFriendlyTruce",
    "debug" => "lightgreen",
  ],
  48 => [
    "name" => clienttranslate("Windstorm"),
    "class" => "Windstorm",
    "description" => clienttranslate("Deal 2 damage, then reveal a mana card from the mana deck. If it's a 1 power mana card, place it on this spell"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 1,
    "icon" => WG_ICON_SET_BASE_1,
    "debug" => "yellow",
  ],

  49 => [
    "name" => clienttranslate("Guilty bond"),
    "class" => "GuiltyBond",
    "description" => clienttranslate("Show your opponent a mana from your hand. Deal 2 damage if they have a mana of the same power in their hand"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 1,
    "icon" => WG_ICON_SET_BASE_1,
    "js_actions" => "actionSelectManaCardFromHand",
    "debug" => "lightgreen",
  ],
  50 => [
    "name" => clienttranslate("Trance state"),
    "class" => "TranceState",
    "description" => clienttranslate("Deal 3 damage, minus 1 damage for each mana card in your hand"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 1,
    "icon" => WG_ICON_SET_BASE_2,
    "debug" => "yellow",
  ],

  51 => [
    "name" => clienttranslate("Renewal"),
    "class" => "Renewal",
    "description" => clienttranslate("Gain mana until you have 4 mana cards in your hand"),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 1,
    "icon" => WG_ICON_SET_BASE_1,
    "debug" => "lightgreen",
  ],
  52 => [
    "name" => clienttranslate("Toxic gift"),
    "class" => "ToxicGift",
    "description" => clienttranslate("Give your opponent a mana card from your hand. If you do, deal damage equal to its power"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 1,
    "icon" => WG_ICON_SET_BASE_1,
    "js_actions" => "actionGiveManaFromHandToOpponent",
    "debug" => "lightgreen",
  ],
  53 => [
    "name" => clienttranslate("Wrath"),
    "class" => "Wrath",
    "description" => clienttranslate("Your opponent may discard 2 mana cards from their hand. If they do not, deal 2 damage."),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 1,
    "icon" => WG_ICON_SET_BASE_1,
    "interaction" => "opponent",
    "js_actions_interaction" => "actionWrath",
    "debug" => "lightgreen",
  ],
  54 => [
    "name" => clienttranslate("Reckless attack"),
    "class" => "RecklessAttack",
    "description" => clienttranslate("Deal 1 damage each time you discard off this spell. If you discard a 4 power mana off this spell, deal 4 damage instead"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_DELAYED,
    "activation_auto" => true,
    "cost" => 1,
    "icon" => WG_ICON_SET_BASE_2,
    "debug" => "red",
  ],


  55 => [
    "name" => clienttranslate("Shared power"),
    "class" => "SharedPower",
    "description" => clienttranslate("You may give your opponent 1 mana card from your hand. If you do, gain 4 mana cards"),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 1,
    "icon" => WG_ICON_SET_BASE_1,
    "debug" => "lightgreen",
    "js_actions" => "actionGiveManaFromHandToOpponent",
  ],
  56 => [
    "name" => clienttranslate("Sleight of hand"),
    "class" => "SleightOfHand",
    "description" => clienttranslate("Gain 1 mana card. Gain additional mana equal to that mana's power"),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 1,
    "icon" => WG_ICON_SET_BASE_1,
    "debug" => "lightgreen",
  ],
  57 => [
    "name" => clienttranslate("Soul pact"),
    "class" => "SoulPact",
    "description" => clienttranslate("If the previous spell you cast this turn was an instant attack spell, gain mana equal to the damage dealt by that spell"),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 1,
    "icon" => WG_ICON_SET_BASE_2,
    "debug" => "red",
  ],
  58 => [
    "name" => clienttranslate("Mirror image"),
    "class" => "MirrorImage",
    "description" => clienttranslate("Gain mana equal to the quantity of mana cards on 1 of your spells"),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 1,
    "icon" => WG_ICON_SET_BASE_2,
    "debug" => "red",
  ],

  59 => [
    "name" => clienttranslate("Battle vision"),
    "class" => "BattleVision",
    "description" => clienttranslate("When your opponent basic attacks, you may discard a mana card of the same power from your hand to block the damage"),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_ONGOING,
    "cost" => 1,
    "icon" => WG_ICON_SET_BASE_2,
    "debug" => "red",
  ],
  60 => [
    "name" => clienttranslate("Silent support"),
    "class" => "SilentSupport",
    "description" => clienttranslate("Each time you discard off this spell, pick up a mana off 1 of your other spells"),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_DELAYED,
    "activation_auto" => false,
    "cost" => 1,
    "icon" => WG_ICON_SET_BASE_2,
    "debug" => "red",
  ],


  61 => [
    "name" => clienttranslate("Power hungry"),
    "class" => "PowerHungry",
    "description" => clienttranslate("Both players basic attack mana cards go to your hand instead of the discard pile"),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_ONGOING,
    "cost" => 1,
    "icon" => WG_ICON_SET_BASE_1,
    "debug" => "yellow",
  ],
  62 => [
    "name" => clienttranslate("Sneaky deal"),
    "class" => "SneakyDeal",
    "description" => clienttranslate("Choose 1: Deal 1 damage, or discard a mana card off 1 of your other spells"),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 1,
    "icon" => WG_ICON_SET_BASE_1,
    "js_actions" => "actionSneakyDeal",
    "debug" => "lightgreen",
  ],
  63 => [
    "name" => clienttranslate("Puppetmaster"),
    "class" => "Puppetmaster",
    "description" => clienttranslate("In order to basic attack, your opponent must use a mana of the same power as you did during the previous basic attack phase"),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_ONGOING,
    "cost" => 1,
    "icon" => WG_ICON_SET_BASE_2,
    "debug" => "red",
  ],
  64 => [
    "name" => clienttranslate("Contamination"),
    "class" => "Contamination",
    "description" => clienttranslate("Place 2 mana cards from your hand on the mana deck. If you do, deal 4 damage"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_BASE_2,
    "debug" => "red",
  ],

  65 => [
    "name" => clienttranslate("Dance of pain"),
    "class" => "DanceOfPain",
    "description" => clienttranslate("Deal 3 damage. Discard or gain mana until you have 2 mana cards in your hand"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_BASE_2,
    "debug" => "red",
  ],
  66 => [
    "name" => clienttranslate("Drain soul"),
    "class" => "DrainSoul",
    "description" => clienttranslate("Your opponent must give you the highest power card mana in their hand. Deal damage equal to that mana's power"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_BASE_1,
    "debug" => "lightgreen",
  ],
  67 => [
    "name" => clienttranslate("Ice Storm"),
    "class" => "IceStorm",
    "description" => clienttranslate("Deal 1 damage for each of your spells with exactly 1 mana card on them."),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_BASE_2,
    "debug" => "red",
  ],
  68 => [
    "name" => clienttranslate("AfterShock"),
    "class" => "AfterShock",
    "description" => clienttranslate("Gain 1 mana card. Place 1 mana card on the mana deck and deal damage equal to its power"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_BASE_2,
    "debug" => "red",
  ],

  69 => [
    "name" => clienttranslate("Bad fortune"),
    "class" => "BadFortune",
    "description" => clienttranslate("Deal 4 damage. Reveal 3 cards from the mana deck. Place any revealed 1 power mana on this spell. Return the rest in any order"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_BASE_1,
    "debug" => "purple",
  ],
  70 => [
    "name" => clienttranslate("Force of nature"),
    "class" => "ForceOfNature",
    "description" => clienttranslate("Deal 4 damage, minus 1 damage for each of your other attack spells that have mana on them"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_SET_BASE_2,
    "debug" => "red",
  ],
];

$this->mana_cards = [
  1 => 32,
  2 => 16,
  3 => 8,
  4 => 4,
];