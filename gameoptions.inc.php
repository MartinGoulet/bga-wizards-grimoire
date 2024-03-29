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
 * gameoptions.inc.php
 *
 * WizardsGrimoire game options description
 * 
 * In this file, you can define your game options (= game variants).
 *   
 * Note: If your game has no variant, you don't have to modify this file.
 *
 * Note²: All options defined in this file should have a corresponding "game state labels"
 *        with the same ID (see "initGameStateLabels" in wizardsgrimoire.game.php)
 *
 * !! It is not a good idea to modify this file when a game is running !!
 *
 */

require_once("modules/php/constants.inc.php");

$game_options = [
    WG_GAME_OPTION_DIFFICULTY_ID => [
        'name' => totranslate('Difficulty'),
        'default' => WG_DIFFICULTY_ADVANCED,
        'values' => [
            WG_DIFFICULTY_BEGINNER => [
                'name' => totranslate('Beginner'),
                'tmdisplay' => totranslate('Beginner'),
                'description' => totranslate('Spell pool of 8 cards, only cards with 1 star')
            ],
            WG_DIFFICULTY_ADVANCED => [
                'name' => totranslate('Advanced'),
                'tmdisplay' => totranslate('Advanced'),
                'description' => totranslate('Spell pool of 10 cards, all cards are available')
            ],
        ]
    ],
    WG_GAME_OPTION_EXT_KICKSTARTER_1_ID => [
        'name' => totranslate('Lost Pages expansion'),
        'default' => WG_OPTION_YES,
        'values' => [
            WG_OPTION_YES => [
                'name' => totranslate('Yes'),
                'tmdisplay' => totranslate('Lost Pages expansion'),
                'description' => totranslate('Add a mini expansion of 10 spell cards from the first kickstarter campaign')
            ],
            WG_OPTION_NO => [
                'name' => totranslate('No'),
            ],
        ]
        
    ],
    WG_GAME_OPTION_SHIFT_SAND_PROMO_ID => [
        'name' => totranslate('Shifting Sands Kickstarter Promo'),
        'default' => WG_OPTION_NO,
        'values' => [
            WG_OPTION_YES => [
                'name' => totranslate('Yes'),
                'tmdisplay' => totranslate('Shifting Sands Kickstarter Promo'),
                'description' => totranslate('Add some spells from the upcoming Shifting Sands Kickstarter campaign')
            ],
            WG_OPTION_NO => [
                'name' => totranslate('No'),
            ],
        ]
    ],
];



/*array(

    
    // note: game variant ID should start at 100 (ie: 100, 101, 102, ...). The maximum is 199.
    100 => array(
                'name' => totranslate('my game option'),    
                'values' => array(

                            // A simple value for this option:
                            1 => array( 'name' => totranslate('option 1') )

                            // A simple value for this option.
                            // If this value is chosen, the value of "tmdisplay" is displayed in the game lobby
                            2 => array( 'name' => totranslate('option 2'), 'tmdisplay' => totranslate('option 2') ),

                            // Another value, with other options:
                            //  description => this text will be displayed underneath the option when this value is selected to explain what it does
                            //  beta=true => this option is in beta version right now (there will be a warning)
                            //  alpha=true => this option is in alpha version right now (there will be a warning, and starting the game will be allowed only in training mode except for the developer)
                            //  nobeginner=true  =>  this option is not recommended for beginners
                            //  firstgameonly=true  =>  this option is recommended only for the first game (discovery option)
                            3 => array( 'name' => totranslate('option 3'), 'description' => totranslate('this option does X'), 'beta' => true, 'nobeginner' => true )
                        ),
                'default' => 1
            ),

    );*/
