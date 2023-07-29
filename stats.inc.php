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
 * stats.inc.php
 *
 * WizardsGrimoire game statistics description
 *
 */

/*
    In this file, you are describing game statistics, that will be displayed at the end of the
    game.
    
    !! After modifying this file, you must use "Reload  statistics configuration" in BGA Studio backoffice
    ("Control Panel" / "Manage Game" / "Your Game")
    
    There are 2 types of statistics:
    _ table statistics, that are not associated to a specific player (ie: 1 value for each game).
    _ player statistics, that are associated to each players (ie: 1 value for each player in the game).

    Statistics types can be "int" for integer, "float" for floating point values, and "bool" for boolean
    
    Once you defined your statistics there, you can start using "initStat", "setStat" and "incStat" method
    in your game logic, using statistics names defined below.
    
    !! It is not a good idea to modify this file when a game is running !!

    If your game is already public on BGA, please read the following before any change:
    http://en.doc.boardgamearena.com/Post-release_phase#Changes_that_breaks_the_games_in_progress
    
    Notes:
    * Statistic index is the reference used in setStat/incStat/initStat PHP method
    * Statistic index must contains alphanumerical characters and no space. Example: 'turn_played'
    * Statistics IDs must be >=10
    * Two table statistics can't share the same ID, two player statistics can't share the same ID
    * A table statistic can have the same ID than a player statistics
    * Statistics ID is the reference used by BGA website. If you change the ID, you lost all historical statistic data. Do NOT re-use an ID of a deleted statistic
    * Statistic name is the English description of the statistic as shown to players
    
*/
require_once("modules/php/constants.inc.php");

$stats_type = [

    // Statistics global to table
    "table" => [

        WG_STAT_TURN_NUMBER => array(
            "id" => 10,
            "name" => totranslate("Number of turns"),
            "type" => "int"
        ),


    ],

    // Statistics existing for each player
    "player" => [

        WG_STAT_TURN_NUMBER => [
            "id" => 10,
            "name" => totranslate("Number of turns"),
            "type" => "int"
        ],

        WG_STAT_WIN_FIRST_CHOICE => [
            "id" => 11,
            "name" => totranslate("Win rate with first spell choice"),
            "type" => "int"
        ],

        WG_STAT_WIN_FIRST_ATTACKER => [
            "id" => 12,
            "name" => totranslate("Win rate with as first attacker"),
            "type" => "int"
        ],

        // Spell drafted
        WG_STAT_NBR_DRAFT => [
            "id" => 20,
            "name" => totranslate("Number of spells drafted"),
            "type" => "int"
        ],

        WG_STAT_NBR_DRAFT_ATTACK => [
            "id" => 21,
            "name" => totranslate("Number of attack spells drafted"),
            "type" => "int"
        ],

        WG_STAT_NBR_DRAFT_REGENERATION => [
            "id" => 22,
            "name" => totranslate("Number of regeneration spells drafted"),
            "type" => "int"
        ],

        WG_STAT_NBR_DRAFT_UTILITY => [
            "id" => 23,
            "name" => totranslate("Number of utility spells drafted"),
            "type" => "int"
        ],


        // Spell damage 
        WG_STAT_NBR_DRAFT_COST_1 => [
            "id" => 25,
            "name" => totranslate("Number of 1 cost spells drafted"),
            "type" => "int"
        ],

        WG_STAT_NBR_DRAFT_COST_2 => [
            "id" => 26,
            "name" => totranslate("Number of 2 cost spells drafted"),
            "type" => "int"
        ],

        WG_STAT_NBR_DRAFT_COST_3 => [
            "id" => 27,
            "name" => totranslate("Number of 3 cost spells drafted"),
            "type" => "int"
        ],

        WG_STAT_NBR_DRAFT_COST_4 => [
            "id" => 28,
            "name" => totranslate("Number of 4 cost spells drafted"),
            "type" => "int"
        ],

        WG_STAT_NBR_DRAFT_COST_5 => [
            "id" => 29,
            "name" => totranslate("Number of 5 cost spells drafted"),
            "type" => "int"
        ],

        // Spell damage 
        WG_STAT_DMG_WHEN_2_SPELLS => [
            "id" => 30,
            "name" => totranslate("Damage when having 2 spells"),
            "type" => "int"
        ],

        WG_STAT_DMG_WHEN_3_SPELLS => [
            "id" => 31,
            "name" => totranslate("Damage when having 3 spells"),
            "type" => "int"
        ],

        WG_STAT_DMG_WHEN_4_SPELLS => [
            "id" => 32,
            "name" => totranslate("Damage when having 4 spells"),
            "type" => "int"
        ],

        WG_STAT_DMG_WHEN_5_SPELLS => [
            "id" => 33,
            "name" => totranslate("Damage when having 5 spells"),
            "type" => "int"
        ],

        WG_STAT_DMG_WHEN_6_SPELLS => [
            "id" => 34,
            "name" => totranslate("Damage when having 6 spells"),
            "type" => "int"
        ],

        WG_STAT_DMG_BASIC_ATTACK => [
            "id" => 38,
            "name" => totranslate("Damage with basic attack"),
            "type" => "int"
        ],

        WG_STAT_NBR_MANA_DRAW => [
            "id" => 70,
            "name" => totranslate("Number of mana cards drawn"),
            "type" => "int"
        ],
    ]
];
