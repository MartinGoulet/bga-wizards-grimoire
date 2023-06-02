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
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_BEGINNER,
  ],
  2 => [
    "name" => clienttranslate("Overload"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_BEGINNER,
  ],
  3 => [
    "name" => clienttranslate("Mutation"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_BEGINNER,
  ],
  4 => [
    "name" => clienttranslate("Stone crush"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_BEGINNER,
  ],
  5 => [
    "name" => clienttranslate("Mist of pain"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_BEGINNER,
  ],
  6 => [
    "name" => clienttranslate("Arcane tactics"),
    "description" => clienttranslate("Gain 7 mana cards from the mana deck. Then place 4 mana cards from your hand on top of the mana deck in any order"),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_BEGINNER,
  ],
  7 => [
    "name" => clienttranslate("Freeze"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_BEGINNER,
  ],
  8 => [
    "name" => clienttranslate("Energy reserve"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_BEGINNER,
  ],
  9 => [
    "name" => clienttranslate("Fracture"),
    "description" => clienttranslate("Gain 4 mana cards. You may move a mana card between 2 of your other spells"),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_BEGINNER,
  ],
  10 => [
    "name" => clienttranslate("Gleam of hope"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_BEGINNER,
  ],

  11 => [
    "name" => clienttranslate("Rejuvenation"),
    "description" => clienttranslate("Choose 1: Gain 4 mana cards, or take 2 mana cards of any power from the discard pile."),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_BEGINNER,
  ],
  12 => [
    "name" => clienttranslate("Shackled motion"),
    "description" => clienttranslate("Choose 1: Gain 4 mana cards, or your opponent must discard their hand"),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_BEGINNER,
  ],
  13 => [
    "name" => clienttranslate("Living wind"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_BEGINNER,
  ],
  14 => [
    "name" => clienttranslate("Coercive agreement"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_INTERMEDIATE,
  ],

  15 => [
    "name" => clienttranslate("Arcane eye"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_INTERMEDIATE,
  ],
  16 => [
    "name" => clienttranslate("Haste"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_BEGINNER,
  ],
  17 => [
    "name" => clienttranslate("Renewed fervor"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_BEGINNER,
  ],
  18 => [
    "name" => clienttranslate("Secret Oath"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_ONGOING,
    "cost" => 2,
    "icon" => WG_ICON_INTERMEDIATE,
  ],
  19 => [
    "name" => clienttranslate("Time distortion"),
    "description" => clienttranslate("Pick up a mana card of 2 of your other spells"),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_BEGINNER,
  ],
  20 => [
    "name" => clienttranslate("Betrayal"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 3,
    "icon" => WG_ICON_BEGINNER,
  ],

  21 => [
    "name" => clienttranslate("Blood lust"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 3,
    "icon" => WG_ICON_INTERMEDIATE,
  ],
  22 => [
    "name" => clienttranslate("Delusion"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 3,
    "icon" => WG_ICON_INTERMEDIATE,
  ],
  23 => [
    "name" => clienttranslate("Vile laughter"),
    "description" => clienttranslate("Deal 6 damage, minus the highest power mana in your hand"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 3,
    "icon" => WG_ICON_BEGINNER,
    "class" => "VileLaughter",
  ],
  24 => [
    "name" => clienttranslate("Doom drop"),
    "description" => clienttranslate("Each time a mana card is discarded of this spell, deal damage equal to that mana's power"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_DELAYED,
    "cost" => 3,
    "icon" => WG_ICON_BEGINNER,
  ],

  25 => [
    "name" => clienttranslate("Energy storm"),
    "description" => clienttranslate("Deal 3 damage. Deal 1 additional damage for each 4 power mana in your hand"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 3,
    "icon" => WG_ICON_BEGINNER,
  ],
  26 => [
    "name" => clienttranslate("Fire blast"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 3,
    "icon" => WG_ICON_BEGINNER,
  ],
  27 => [
    "name" => clienttranslate("Painful vision"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 3,
    "icon" => WG_ICON_INTERMEDIATE,
  ],
  28 => [
    "name" => clienttranslate("Possessed"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 3,
    "icon" => WG_ICON_INTERMEDIATE,
  ],
  29 => [
    "name" => clienttranslate("Second strike"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 3,
    "icon" => WG_ICON_BEGINNER,
  ],
  30 => [
    "name" => clienttranslate("Trap attack"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 3,
    "icon" => WG_ICON_BEGINNER,
  ],
  31 => [
    "name" => clienttranslate("Energy shield"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 3,
    "icon" => WG_ICON_INTERMEDIATE,
  ],
  32 => [
    "name" => clienttranslate("Growth"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_ONGOING,
    "cost" => 3,
    "icon" => WG_ICON_INTERMEDIATE,
  ],
  33 => [
    "name" => clienttranslate("Fury"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 4,
    "icon" => WG_ICON_BEGINNER,
  ],
  34 => [
    "name" => clienttranslate("Hellstorm"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 4,
    "icon" => WG_ICON_BEGINNER,
  ],


  35 => [
    "name" => clienttranslate("Shadow attack"),
    "description" => clienttranslate("Discard a mana card off 1 of your other spells. Deal damage and gain mana equal to that mana<s power"),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 4,
    "icon" => WG_ICON_BEGINNER,
  ],
  36 => [
    "name" => clienttranslate("Crushing blow"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 5,
    "icon" => WG_ICON_BEGINNER,
  ],
  37 => [
    "name" => clienttranslate("False face"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_ONGOING,
    "cost" => 1,
    "icon" => WG_ICON_ADVANCE,
  ],
  38 => [
    "name" => clienttranslate("Quick swap"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_DELAYED,
    "cost" => 1,
    "icon" => WG_ICON_ADVANCE,
  ],

  39 => [
    "name" => clienttranslate("Lullaby"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_ONGOING,
    "cost" => 1,
    "icon" => WG_ICON_ADVANCE,
  ],
  40 => [
    "name" => clienttranslate("Amnesia"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 1,
    "icon" => WG_ICON_ADVANCE,
  ],

  41 => [
    "name" => clienttranslate("Hoodwink"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 3,
    "icon" => WG_ICON_ADVANCE,
  ],
  42 => [
    "name" => clienttranslate("Wild bloom"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_DELAYED,
    "cost" => 2,
    "icon" => WG_ICON_ADVANCE,
  ],
  43 => [
    "name" => clienttranslate("Tsunami"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_ADVANCE,
  ],
  44 => [
    "name" => clienttranslate("Twist of fate"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 3,
    "icon" => WG_ICON_ADVANCE,
  ],


  45 => [
    "name" => clienttranslate("Fatal flaw"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_ADVANCE,
  ],
  46 => [
    "name" => clienttranslate("Affliction"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_ADVANCE,
  ],
  47 => [
    "name" => clienttranslate("Friendly truce"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_BEGINNER,
  ],
  48 => [
    "name" => clienttranslate("Windstorm"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 1,
    "icon" => WG_ICON_BEGINNER,
  ],

  49 => [
    "name" => clienttranslate("Guilty bond"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 1,
    "icon" => WG_ICON_BEGINNER,
  ],
  50 => [
    "name" => clienttranslate("Trance state"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 1,
    "icon" => WG_ICON_INTERMEDIATE,
  ],

  51 => [
    "name" => clienttranslate("Renewal"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 1,
    "icon" => WG_ICON_BEGINNER,
  ],
  52 => [
    "name" => clienttranslate("Toxic gift"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 1,
    "icon" => WG_ICON_BEGINNER,
  ],
  53 => [
    "name" => clienttranslate("Wrath"),
    "description" => clienttranslate("Your opponent may discard 2 mana cards from their hand. If they do not, deal 2 damage."),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 1,
    "icon" => WG_ICON_BEGINNER,
  ],
  54 => [
    "name" => clienttranslate("Reckless attack"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_DELAYED,
    "cost" => 1,
    "icon" => WG_ICON_INTERMEDIATE,
  ],


  55 => [
    "name" => clienttranslate("Shared power"),
    "description" => clienttranslate("You may give your opponent 1 mana card from your hand. If you do, gain 4 mana cards"),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 1,
    "icon" => WG_ICON_BEGINNER,
  ],
  56 => [
    "name" => clienttranslate("Sleight of hand"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 1,
    "icon" => WG_ICON_BEGINNER,
  ],
  57 => [
    "name" => clienttranslate("Soul pact"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 1,
    "icon" => WG_ICON_INTERMEDIATE,
  ],
  58 => [
    "name" => clienttranslate("Mirror image"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_REGENERATION,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 1,
    "icon" => WG_ICON_INTERMEDIATE,
  ],

  59 => [
    "name" => clienttranslate("Battle vision"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_ONGOING,
    "cost" => 1,
    "icon" => WG_ICON_INTERMEDIATE,
  ],
  60 => [
    "name" => clienttranslate("Silent support"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_DELAYED,
    "cost" => 1,
    "icon" => WG_ICON_INTERMEDIATE,
  ],


  61 => [
    "name" => clienttranslate("Power hungry"),
    "description" => clienttranslate("Both players basic attack mana cards go to your hand instead of the discard pile"),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_ONGOING,
    "cost" => 1,
    "icon" => WG_ICON_BEGINNER,
  ],
  62 => [
    "name" => clienttranslate("Sneaky deal"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 1,
    "icon" => WG_ICON_BEGINNER,
  ],
  63 => [
    "name" => clienttranslate("Puppetmaster"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_UTILITY,
    "activation" => WG_SPELL_ACTIVATION_ONGOING,
    "cost" => 1,
    "icon" => WG_ICON_INTERMEDIATE,
  ],
  64 => [
    "name" => clienttranslate("Contamination"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_INTERMEDIATE,
  ],

  65 => [
    "name" => clienttranslate("Dance of pain"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_INTERMEDIATE,
  ],
  66 => [
    "name" => clienttranslate("Drain soul"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_BEGINNER,
  ],
  67 => [
    "name" => clienttranslate("Ice Storm"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_INTERMEDIATE,
  ],
  68 => [
    "name" => clienttranslate("AfterShock"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_INTERMEDIATE,
  ],

  69 => [
    "name" => clienttranslate("Bad fortune"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_BEGINNER,
  ],
  70 => [
    "name" => clienttranslate("Force of nature"),
    "description" => clienttranslate(""),
    "type" => WG_SPELL_TYPE_DAMAGE,
    "activation" => WG_SPELL_ACTIVATION_INSTANT,
    "cost" => 2,
    "icon" => WG_ICON_INTERMEDIATE,
  ],
];

$this->mana_cards = [
  1 => 32,
  2 => 16,
  3 => 8,
  4 => 4,
];