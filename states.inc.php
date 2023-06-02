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
 * states.inc.php
 *
 * WizardsGrimoire game states description
 *
 */

/*
   Game state machine is a tool used to facilitate game developpement by doing common stuff that can be set up
   in a very easy way from this configuration file.

   Please check the BGA Studio presentation about game state to understand this, and associated documentation.

   Summary:

   States types:
   _ activeplayer: in this type of state, we expect some action from the active player.
   _ multipleactiveplayer: in this type of state, we expect some action from multiple players (the active players)
   _ game: this is an intermediary state where we don't expect any actions from players. Your game logic must decide what is the next game state.
   _ manager: special type for initial and final state

   Arguments of game states:
   _ name: the name of the GameState, in order you can recognize it on your own code.
   _ description: the description of the current game state is always displayed in the action status bar on
                  the top of the game. Most of the time this is useless for game state with "game" type.
   _ descriptionmyturn: the description of the current game state when it's your turn.
   _ type: defines the type of game states (activeplayer / multipleactiveplayer / game / manager)
   _ action: name of the method to call when this game state become the current game state. Usually, the
             action method is prefixed by "st" (ex: "stMyGameStateName").
   _ possibleactions: array that specify possible player actions on this step. It allows you to use "checkAction"
                      method on both client side (Javacript: this.checkAction) and server side (PHP: self::checkAction).
   _ transitions: the transitions are the possible paths to go from a game state to another. You must name
                  transitions in order to use transition names in "nextState" PHP method, and use IDs to
                  specify the next game state for each transition.
   _ args: name of the method to call to retrieve arguments for this gamestate. Arguments are sent to the
           client side to be used on "onEnteringState" or to set arguments in the gamestate description.
   _ updateGameProgression: when specified, the game progression is updated (=> call to your getGameProgression
                            method).
*/

//    !! It is not a good idea to modify this file when a game is running !!

require_once("modules/php/constants.inc.php");

$basicGameStates = [

    // The initial state. Please do not modify.
    ST_BGA_GAME_SETUP => [
        "name" => "gameSetup",
        "description" => clienttranslate("Game setup"),
        "type" => "manager",
        "action" => "stGameSetup",
        "transitions" => ["" => ST_PLAYER_NEW_TURN]
    ],

    // Final state.
    // Please do not modify.
    ST_END_GAME => [
        "name" => "gameEnd",
        "description" => clienttranslate("End of game"),
        "type" => "manager",
        "action" => "stGameEnd",
        "args" => "argGameEnd",
    ],
];

$chooseSpellStates = [
    ST_PLAYER_NEW_TURN => [
        "name" => "playerNewTurn",
        "type" => "game",
        "action" => "stNewTurn",
        "transitions" => [
            "" => ST_CHOOSE_NEW_SPELL
        ]
    ],

    ST_CHOOSE_NEW_SPELL => [
        "name" => "chooseNewSpell",
        "description" => clienttranslate('${actplayer} must choose a new spell'),
        "descriptionmyturn" => clienttranslate('${you} must choose a new spell'),
        "type" => "activeplayer",
        "possibleactions" => ["chooseSpell", "pass"],
        "transitions" => [
            "next_player" => ST_NEXT_PLAYER,
            "end" => ST_SPELL_COOL_DOWN
        ]
    ],
];

$spellCoolDownStates = [
    ST_SPELL_COOL_DOWN => [
        "name" => "spellCoolDown",
        "type" => "game",
        "action" => "stSpellCoolDown",
        "transitions" => [
            "" => ST_GAIN_MANA
        ]
    ]
];

$gainManaStates = [
    ST_GAIN_MANA => [
        "name" => "gainMana",
        "type" => "game",
        "action" => "stGainMana",
        "transitions" => [
            "" => ST_CAST_SPELL
        ]
    ]
];

$castSpellsStates = [
    ST_CAST_SPELL => [
        "name" => "castSpell",
        "description" => clienttranslate('${actplayer} may cast a spell or pass'),
        "descriptionmyturn" => clienttranslate('${you} may cast a spell or pass'),
        "type" => "activeplayer",
        "possibleactions" => ["castSpell", "pass"],
        "transitions" => [
            "" => ST_BASIC_ATTACK
        ]
    ],
];

$basicAttackStates = [
    ST_BASIC_ATTACK => [
        "name" => "basicAttack",
        "description" => clienttranslate('${actplayer} may discard a mana card to deal damage'),
        "descriptionmyturn" => clienttranslate('${you} may discard a mana card to deal damage'),
        "type" => "activeplayer",
        "possibleactions" => ["basicAttack", "pass"],
        "transitions" => [
            "" => ST_SPELL_COOL_DOWN
        ]
    ],

    ST_NEXT_PLAYER => [
        "name" => "playerEndTurn",
        "type" => "game",
        "action" => "stNextPlayer",
        "transitions" => [
            "" => ST_PLAYER_NEW_TURN
        ]
    ]
];

$machinestates =
    $basicGameStates +
    $chooseSpellStates +
    $spellCoolDownStates +
    $gainManaStates +
    $castSpellsStates +
    $basicAttackStates;
