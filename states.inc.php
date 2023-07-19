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

    ST_PRE_END_OF_GAME => [
        'name' => 'preEndOfGame',
        'type' => 'game',
        'action' => 'stPreEndOfGame',
        'transitions' => ["" => ST_END_GAME],
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
        "phase" => 1,
        "name" => "playerNewTurn",
        "type" => "game",
        "args" => "argPlayerNewTurn",
        "action" => "stNewTurn",
        "transitions" => [
            "spell" => ST_CHOOSE_NEW_SPELL_START,
            "discard" => ST_DISCARD_MANA,
        ]
    ],

    ST_DISCARD_MANA => [
        "phase" => 1,
        "name" => "discardMana",
        "description" => clienttranslate('${actplayer} must discard to 10 mana cards'),
        "descriptionmyturn" => clienttranslate('${you} must discard to 10 mana cards'),
        "args" => "argBase",
        "type" => "activeplayer",
        "possibleactions" => ["discardMana"],
        "transitions" => [
            "" => ST_CHOOSE_NEW_SPELL_START,
        ]
    ],

    ST_CHOOSE_NEW_SPELL_START => [
        "phase" => 1,
        "name" => "preChooseNewSpell",
        "type" => "game",
        "action" => "stPreChooseNewSpell",
        "transitions" => [
            "next" => ST_CHOOSE_NEW_SPELL,
            "end" => ST_SPELL_COOL_DOWN,
        ]
    ],

    ST_CHOOSE_NEW_SPELL => [
        "phase" => 1,
        "name" => "chooseNewSpell",
        "description" => clienttranslate('${actplayer} must choose a new spell'),
        "descriptionReplace" => clienttranslate('${actplayer} may choose to replace a spell or pass'),
        "descriptionmyturn" => clienttranslate('${you} must choose a new spell'),
        "descriptionmyturnReplace" => clienttranslate('${you} may choose to replace a spell or pass'),
        "args" => "argBase",
        "type" => "activeplayer",
        "possibleactions" => ["chooseSpell", "replaceSpell", "pass"],
        "transitions" => [
            "next_player" => ST_NEXT_PLAYER,
            "pass" => ST_SPELL_COOL_DOWN,
            "end" => ST_SPELL_COOL_DOWN,
        ]
    ],
];

$spellCoolDownStates = [

    ST_SPELL_COOL_DOWN => [
        "phase" => 2,
        "name" => "spellCoolDown",
        "type" => "game",
        "action" => "stSpellCoolDownInstantDelayed",
        "transitions" => [
            "next" => ST_SPELL_COOL_DOWN_ONGOING,
            "delayed" => ST_SPELL_CD_ACTIVATE_DELAYED,
            "dead" => ST_PRE_END_OF_GAME,
        ]
    ],

    ST_SPELL_COOL_DOWN_ONGOING => [
        "phase" => 2,
        "name" => "spellCoolDown",
        "type" => "game",
        "action" => "stSpellCoolDownOngoing",
        "transitions" => [
            "" => ST_GAIN_MANA,
        ]
    ],
];

$gainManaStates = [
    ST_GAIN_MANA => [
        "phase" => 3,
        "name" => "gainMana",
        "type" => "game",
        "action" => "stGainMana",
        "transitions" => [
            "" => ST_CAST_SPELL_START
        ]
    ]
];

$castSpellsStates = [
    ST_CAST_SPELL_START => [
        "phase" => 4,
        "name" => "preCastSpell",
        "type" => "game",
        "action" => "stPreCastSpell",
        "transitions" => [
            "" => ST_CAST_SPELL,
        ]
    ],

    ST_CAST_SPELL => [
        "phase" => 4,
        "name" => "castSpell",
        "description" => clienttranslate('${actplayer} may cast a spell or pass'),
        "descriptionmyturn" => clienttranslate('${you} may cast a spell or pass'),
        "type" => "activeplayer",
        "args" => "argCastSpell",
        "possibleactions" => ["castSpell", "pass", "undo"],
        "transitions" => [
            "cast" => ST_CAST_SPELL_START,
            "undo" => ST_CAST_SPELL_START,
            "pass" => ST_BASIC_ATTACK_START,
            "player" => ST_CAST_SPELL_INTERACTION,
            "opponent" => ST_CAST_SPELL_SWITCH_OPPONENT,
            "dead" => ST_PRE_END_OF_GAME,
            "delayed" => ST_CAST_SPELL_CD_ACTIVATE_DELAYED,
            "delayed_opponent" => ST_CAST_SPELL_SWITCH_TO_OPPONENT,
        ]
    ],

    ST_CAST_SPELL_SWITCH_OPPONENT => [
        "phase" => 4,
        "name" => "castSpellSwitchOpponent",
        "type" => "game",
        "action" => "stSwitchToOpponent",
        "transitions" => [
            "" => ST_CAST_SPELL_INTERACTION,
        ]
    ],

    ST_CAST_SPELL_INTERACTION => array(
        "phase" => 4,
        "name" => "castSpellInteraction",
        "description" => clienttranslate('${actplayer} must conclude the effect of the spell'),
        "descriptionmyturn" => clienttranslate('${you} must conclude the effect of the spell'),
        "type" => "activeplayer",
        "args" => "argCastSpellInteraction",
        "possibleactions" => ["castSpellInteraction"],
        "transitions" => [
            "return" => ST_CAST_SPELL_RETURN_CURRENT_PLAYER,
            "dead" => ST_PRE_END_OF_GAME,
        ]
    ),

    ST_CAST_SPELL_RETURN_CURRENT_PLAYER => [
        "phase" => 4,
        "name" => "castSpellReturnCurrentPlayer",
        "type" => "game",
        "action" => "stReturnToCurrentPlayer",
        "transitions" => [
            "" => ST_CAST_SPELL_START,
        ]
    ],

    ST_CAST_SPELL_SWITCH_TO_OPPONENT => [
        "phase" => 4,
        "name" => "castSpellSwitchPlayer",
        "type" => "game",
        "action" => "stSwithPlayer",
        "transitions" => [
            "" => ST_CAST_SPELL_CD_OPPONENT_ACTIVATE_DELAYED,
        ]
    ],

    ST_CAST_SPELL_SWITCH_TO_CURRENT_PLAYER => [
        "phase" => 4,
        "name" => "castSpellSwitchPlayer",
        "type" => "game",
        "action" => "stSwithPlayer",
        "transitions" => [
            "" => ST_CAST_SPELL_START,
        ]
    ],
];

$basicAttackStates = [
    ST_BASIC_ATTACK_START => [
        "phase" => 5,
        "name" => "preBasicAttack",
        "type" => "game",
        "action" => "stPreBasicAttack",
        "transitions" => [
            "next" => ST_BASIC_ATTACK,
            "end" => ST_NEXT_PLAYER,
        ]
    ],

    ST_BASIC_ATTACK => [
        "phase" => 5,
        "name" => "basicAttack",
        "description" => clienttranslate('${actplayer} may discard a mana card to deal damage'),
        "descriptionmyturn" => clienttranslate('${you} may discard a mana card to deal damage'),
        "args" => "argBasicAttack",
        "type" => "activeplayer",
        "possibleactions" => ["basicAttack", "pass", "undo"],
        "transitions" => [
            "attack" => ST_BASIC_ATTACK_DAMAGE,
            "undo" => ST_CAST_SPELL_START,
            "pass" => ST_NEXT_PLAYER,
            "battle_vision" => ST_BASIC_ATTACK_SWITCH_OPPONENT,
        ]
    ],

    ST_BASIC_ATTACK_SWITCH_OPPONENT => [
        "phase" => 5,
        "name" => "basicAttackSwitchOpponent",
        "type" => "game",
        "action" => "stSwitchToOpponent",
        "transitions" => [
            "" => ST_BASIC_ATTACK_BATTLE_VISION,
        ]
    ],

    ST_BASIC_ATTACK_BATTLE_VISION => [
        "phase" => 5,
        "name" => "basicAttackBattleVision",
        "description" => clienttranslate('${actplayer} may discard a mana card to block the damage'),
        "descriptionmyturn" => clienttranslate('${you} may discard a mana card to block the damage'),
        "args" => "argBattleVision",
        "type" => "activeplayer",
        "possibleactions" => ["blockBasicAttack", "pass"],
        "transitions" => [
            "pass" => ST_BASIC_ATTACK_RETURN_CURRENT_PLAYER,
            "block" => ST_BASIC_ATTACK_END,
        ]
    ],

    ST_BASIC_ATTACK_RETURN_CURRENT_PLAYER => [
        "phase" => 5,
        "name" => "basicAttackReturnCurrentPlayer",
        "type" => "game",
        "action" => "stReturnToCurrentPlayer",
        "transitions" => [
            "" => ST_BASIC_ATTACK_DAMAGE,
        ]
    ],

    ST_BASIC_ATTACK_DAMAGE => [
        "phase" => 5,
        "name" => "basicAttackDamage",
        "type" => "game",
        "action" => "stBasicAttackDamage",
        "transitions" => [
            "attack" => ST_BASIC_ATTACK_END,
            "dead" => ST_PRE_END_OF_GAME,
        ]
    ],


    ST_BASIC_ATTACK_END => [
        "phase" => 5,
        "name" => "basicAttackDamageEnd",
        "type" => "game",
        "action" => "stBasicAttackEnd",
        "transitions" => [
            "" => ST_NEXT_PLAYER,
        ]
    ],

    ST_NEXT_PLAYER => [
        "phase" => 5,
        "name" => "playerEndTurn",
        "type" => "game",
        "action" => "stNextPlayer",
        "transitions" => [
            "" => ST_PLAYER_NEW_TURN
        ]
    ]
];

$stSpellCooldownActiveDeplayed = WizardsGrimoire::getActiveDelayedSpellStates(
    2,
    ST_SPELL_CD_ACTIVATE_DELAYED,
    ST_SPELL_CD_CAST_SPELL_SWITCH_OPPONENT,
    ST_SPELL_CD_CAST_SPELL_INTERACTION,
    ST_SPELL_CD_CAST_SPELL_RETURN_CURRENT_PLAYER,
    ST_SPELL_COOL_DOWN_ONGOING,
);

$stCastSpellActiveDeplayed = WizardsGrimoire::getActiveDelayedSpellStates(
    4,
    ST_CAST_SPELL_CD_ACTIVATE_DELAYED,
    ST_CAST_SPELL_CD_SWITCH_OPPONENT,
    ST_CAST_SPELL_CD_INTERACTION,
    ST_CAST_SPELL_CD_RETURN_CURRENT_PLAYER,
    ST_CAST_SPELL_START
);

$stCastSpellActiveDeplayedOpponent = WizardsGrimoire::getActiveDelayedSpellStates(
    4,
    ST_CAST_SPELL_CD_OPPONENT_ACTIVATE_DELAYED,
    ST_CAST_SPELL_CD_OPPONENT_SWITCH_OPPONENT,
    ST_CAST_SPELL_CD_OPPONENT_INTERACTION,
    ST_CAST_SPELL_CD_OPPONENT_RETURN_CURRENT_PLAYER,
    ST_CAST_SPELL_SWITCH_TO_CURRENT_PLAYER
);

$machinestates =
    $basicGameStates +
    $chooseSpellStates +
    $spellCoolDownStates +
    $gainManaStates +
    $castSpellsStates +
    $basicAttackStates +

    $stSpellCooldownActiveDeplayed +
    $stCastSpellActiveDeplayed +
    $stCastSpellActiveDeplayedOpponent;
