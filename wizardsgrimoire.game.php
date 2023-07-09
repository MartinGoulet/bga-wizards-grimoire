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
 * wizardsgrimoire.game.php
 *
 * This is the main file for your game logic.
 *
 * In this PHP file, you are going to defines the rules of the game.
 *
 */


$swdNamespaceAutoload = function ($class) {
    $classParts = explode('\\', $class);
    if ($classParts[0] == 'WizardsGrimoire') {
        array_shift($classParts);
        $file = dirname(__FILE__) . '/modules/php/' . implode(DIRECTORY_SEPARATOR, $classParts) . '.php';
        if (file_exists($file)) {
            require_once $file;
        } else {
            var_dump('Cannot find file : ' . $file);
        }
    }
};
spl_autoload_register($swdNamespaceAutoload, true, true);

require_once(APP_GAMEMODULE_PATH . 'module/table/table.game.php');
require_once('modules/php/actions.php');
require_once('modules/php/args.php');
require_once('modules/php/debug.php');
require_once('modules/php/states.php');
require_once('modules/php/constants.inc.php');

use WizardsGrimoire\Core\ActionTrait;
use WizardsGrimoire\Core\ArgsTrait;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;
use WizardsGrimoire\Core\StateTrait;
use WizardsGrimoire\DebugTrait;
use WizardsGrimoire\Objects\CardLocation;

class WizardsGrimoire extends Table {
    use ActionTrait;
    use ArgsTrait;
    use StateTrait;
    use DebugTrait;

    /** @var WizardsGrimoire */
    public static $instance = null;

    /** @var Deck */
    public $deck_spells;

    /** @var Deck */
    public $deck_manas;

    public $card_types;
    public $mana_cards;

    function __construct() {
        // Your global variables labels:
        //  Here, you can assign labels to global variables you are using for this game.
        //  You can use any number of global variables with IDs between 10 and 99.
        //  If your game has options (variants), you also have to associate here a label to
        //  the corresponding ID in gameoptions.inc.php.
        // Note: afterwards, you can get/set the global variables with getGameStateValue/setGameStateInitialValue/setGameStateValue
        parent::__construct();

        self::initGameStateLabels(array(
            WG_VAR_SLOT_COUNT => 10,
            WG_VAR_CURRENT_PLAYER => 11,
            WG_VAR_SPELL_PLAYED => 12,
            WG_VAR_INTERACTION_PLAYER => 13,
            WG_VAR_SKIP_INTERACTION => 14,
            WG_VAR_DISCOUNT_NEXT_SPELL => 15,
            WG_VAR_DISCOUNT_ATTACK_SPELL => 16,
            WG_VAR_AMNESIA => 17,
            WG_VAR_IS_ACTIVE_GROWTH => 18,
            WG_VAR_CONSECUTIVELY_ATTACK_SPELL_CAST => 19,
            WG_VAR_IS_ACTIVE_PUPPETMASTER => 20,
            WG_VAR_PREVIOUS_BASIC_ATTACK_POWER => 21,
            WG_VAR_IS_ACTIVE_SECRET_OATH => 22,
            WG_VAR_PREVIOUS_SPELL_PLAYED => 23,
            WG_VAR_PREVIOUS_SPELL_DAMAGE => 24,
            WG_VAR_IS_ACTIVE_POWER_HUNGRY => 25,
            WG_VAR_IS_ACTIVE_BATTLE_VISION => 26,
            WG_VAR_CURRENT_BASIC_ATTACK_POWER => 27,
            WG_VAR_IS_ACTIVE_LULLABY => 28,

            WG_GAME_OPTION_DIFFICULTY => WG_GAME_OPTION_DIFFICULTY_ID,
            WG_GAME_OPTION_EXT_KICKSTARTER_1 => WG_GAME_OPTION_EXT_KICKSTARTER_1_ID,
        ));

        self::$instance = $this;

        $this->deck_spells = self::getNew("module.common.deck");
        $this->deck_spells->init("spells");

        $this->deck_manas = self::getNew("module.common.deck");
        $this->deck_manas->init("manas");
    }

    public static function get() {
        return self::$instance;
    }

    protected function getGameName() {
        // Used for translations and stuff. Please do not modify.
        return "wizardsgrimoire";
    }

    /*
        setupNewGame:
        
        This method is called only once, when a new game is launched.
        In this method, you must setup the game according to the game rules, so that
        the game is ready to be played.
    */
    protected function setupNewGame($players, $options = array()) {
        // Set the colors of the players with HTML color code
        // The default below is red/green/blue/orange/brown
        // The number of colors defined here must correspond to the maximum number of players allowed for the gams
        $gameinfos = self::getGameinfos();
        $default_colors = $gameinfos['player_colors'];

        // Create players
        // Note: if you added some extra field on "player" table in the database (dbmodel.sql), you can initialize it there.
        $sql = "INSERT INTO player (player_id, player_color, player_canal, player_name, player_avatar, player_score) VALUES ";
        $values = array();
        foreach ($players as $player_id => $player) {
            $color = array_shift($default_colors);
            $values[] = "('" . $player_id . "','$color','" . $player['player_canal'] . "','" . addslashes($player['player_name']) . "','" . addslashes($player['player_avatar']) . "', 60)";
        }
        $sql .= implode(',', $values);
        self::DbQuery($sql);
        self::reattributeColorsBasedOnPreferences($players, $gameinfos['player_colors']);
        self::reloadPlayersBasicInfos();

        /************ Start the game initialization *****/

        // Init global values with their initial values
        self::setGameStateInitialValue(WG_VAR_CURRENT_PLAYER, 0);
        self::setGameStateInitialValue(WG_VAR_INTERACTION_PLAYER, 0);
        self::setGameStateInitialValue(WG_VAR_SPELL_PLAYED, 0);
        self::setGameStateInitialValue(WG_VAR_SKIP_INTERACTION, 0);
        self::setGameStateInitialValue(WG_VAR_DISCOUNT_NEXT_SPELL, 0);
        self::setGameStateInitialValue(WG_VAR_DISCOUNT_ATTACK_SPELL, 0);
        self::setGameStateInitialValue(WG_VAR_AMNESIA, 0);
        self::setGameStateInitialValue(WG_VAR_IS_ACTIVE_BATTLE_VISION, 0);
        self::setGameStateInitialValue(WG_VAR_IS_ACTIVE_GROWTH, 0);
        self::setGameStateInitialValue(WG_VAR_IS_ACTIVE_LULLABY, 0);
        self::setGameStateInitialValue(WG_VAR_IS_ACTIVE_POWER_HUNGRY, 0);
        self::setGameStateInitialValue(WG_VAR_IS_ACTIVE_PUPPETMASTER, 0);
        self::setGameStateInitialValue(WG_VAR_IS_ACTIVE_SECRET_OATH, 0);
        self::setGameStateInitialValue(WG_VAR_CONSECUTIVELY_ATTACK_SPELL_CAST, 0);
        self::setGameStateInitialValue(WG_VAR_CURRENT_BASIC_ATTACK_POWER, 0);
        self::setGameStateInitialValue(WG_VAR_PREVIOUS_BASIC_ATTACK_POWER, 0);
        self::setGameStateInitialValue(WG_VAR_PREVIOUS_SPELL_PLAYED, 0);
        self::setGameStateInitialValue(WG_VAR_PREVIOUS_SPELL_DAMAGE, 0);

        // Init game statistics
        // (note: statistics used in this file must be defined in your stats.inc.php file)
        //self::initStat( 'table', 'table_teststat1', 0 );    // Init a table statistics
        //self::initStat( 'player', 'player_teststat1', 0 );  // Init a player statistics (for all players)
        self::initStat('table', WG_STAT_TURN_NUMBER, 0);
        self::initStat('player', WG_STAT_TURN_NUMBER, 0);

        $gameOptionDifficulty = intval(self::getGameStateValue(WG_GAME_OPTION_DIFFICULTY));
        $gameOptionKickStarter1 = intval(self::getGameStateValue(WG_GAME_OPTION_EXT_KICKSTARTER_1));

        $slot_count = $gameOptionDifficulty == WG_DIFFICULTY_BEGINNER ? 8 : 10;
        self::setGameStateInitialValue(WG_VAR_SLOT_COUNT, $slot_count);

        $cards_types = array_filter($this->card_types, function ($card_type) use ($gameOptionDifficulty, $gameOptionKickStarter1) {
            switch($card_type['icon']) {
                case WG_ICON_SET_BASE_1:
                    return true;
                case WG_ICON_SET_BASE_2:
                    return $gameOptionDifficulty == WG_DIFFICULTY_ADVANCED;
                case WG_ICON_SET_KICKSTARTER_1:
                    return $gameOptionKickStarter1 == WG_OPTION_YES;
            }
        });

        $cards = [];
        foreach ($cards_types as $id => $card) {
            $cards[] = ['type' => $id, 'type_arg' => 0, 'nbr' => 1];
        }
        $this->deck_spells->createCards($cards);
        $this->deck_spells->shuffle(CardLocation::Deck());

        $cards = [];
        foreach ($this->mana_cards as $number => $count) {
            $cards[] = ['type' => $number, 'type_arg' => 0, 'nbr' => intval($count)];
        }
        $this->deck_manas->createCards($cards);
        $this->deck_manas->shuffle(CardLocation::Deck());

        for ($i = 1; $i <= $slot_count; $i++) {
            $this->deck_spells->pickCardForLocation(CardLocation::Deck(), CardLocation::SpellSlot(), $i);
        }

        // Activate first player (which is in general a good idea :) )
        $this->activeNextPlayer();

        /************ End of the game initialization *****/
    }

    /*
        getAllDatas: 
        
        Gather all informations about current game situation (visible by the current player).
        
        The method is called each time the game interface is displayed to a player, ie:
        _ when the game starts
        _ when a player refreshes the game page (F5)
    */
    protected function getAllDatas() {
        $result = array();

        $current_player_id = self::getCurrentPlayerId();    // !! We must only return informations visible by this player !!

        // Get information about players
        // Note: you can retrieve some extra field you added for "player" table in "dbmodel.sql" if you need it.
        $sql = "SELECT player_id id, player_score score FROM player ";
        $result['players'] = self::getCollectionFromDb($sql);

        $result['card_types'] = $this->card_types;

        $result['slot_count'] = intval(self::getGameStateValue(WG_VAR_SLOT_COUNT));
        $result['slot_cards'] = array_values($this->deck_spells->getCardsInLocation(CardLocation::SpellSlot()));

        $result['spells'] = [
            'deck' => array_values(Game::anonynizeCards(
                $this->deck_spells->getCardsInLocation(CardLocation::Deck())
            )),
            'discard' => array_values(
                $this->deck_spells->getCardsInLocation(CardLocation::Discard())
            ),
        ];

        $result['manas'] = [
            'deck' => array_values(Game::anonynizeCards(
                $this->deck_manas->getCardsInLocation(CardLocation::Deck())
            )),
            'discard' => array_values(
                $this->deck_manas->getCardsInLocation(CardLocation::Discard())
            ),
            "attack" => array_values(
                $this->deck_manas->getCardsInLocation(CardLocation::BasicAttack())
            ),
            "revealed" => array_values(
                $this->deck_manas->getCardsInLocation(CardLocation::ManaRevelead())
            )
        ];

        $players = self::loadPlayersBasicInfos();
        $result['player_board'] = [];
        foreach ($players as $player_id => $player) {
            $result['player_board'][$player_id] = [
                'spells' => array_values(SpellCard::getCardsFromRepertoire($player_id)),
                'manas' => [],
            ];
            for ($i = 1; $i <= 6; $i++) {
                $cards = $this->deck_manas->getCardsInLocation(CardLocation::PlayerManaCoolDown($player_id, $i), null, 'location_arg');
                $cards = Game::anonynizeCards($cards, $player_id != $current_player_id);
                $result['player_board'][$player_id]['manas'][$i] = array_values($cards);
            }

            $cards = $this->deck_manas->getCardsInLocation(CardLocation::Hand(), $player_id);
            $cards = Game::anonynizeCards($cards, $player_id != $current_player_id);
            $result['player_board'][$player_id]['hand'] = array_values($cards);
        }

        $result['players_order'] = array_keys(Players::getPlayersInOrder($current_player_id));

        // $result['debug_spells'] = self::getCollectionFromDB("SELECT * FROM spells");
        // $result['debug_manas'] = self::getCollectionFromDB("SELECT * FROM manas");
        // $result['debug_globals'] = self::getCollectionFromDB("SELECT * FROM global");

        $result['opponent_id'] = Players::getOpponentIdOf($current_player_id);

        $result['globals'] = [
            "previous_basic_attack" => Globals::getPreviousBasicAttackPower(),
        ];

        return $result;
    }

    /*
        getGameProgression:
        
        Compute and return the current game progression.
        The number returned must be an integer beween 0 (=the game just started) and
        100 (= the game is finished or almost finished).
    
        This method is called each time we are in a game state with the "updateGameProgression" property set to true 
        (see states.inc.php)
    */
    function getGameProgression() {
        // TODO: compute and return the game progression

        return 0;
    }


    //////////////////////////////////////////////////////////////////////////////
    //////////// Utility functions
    ////////////    

    /*
        In this space, you can put any utility methods useful for your game logic
    */


    //////////////////////////////////////////////////////////////////////////////
    //////////// Player actions
    //////////// 

    /*
        Each time a player is doing some game action, one of the methods below is called.
        (note: each method below must match an input method in wizardsgrimoire.action.php)
    */

    /*
    
    Example:

    function playCard( $card_id )
    {
        // Check that this is the player's turn and that it is a "possible action" at this game state (see states.inc.php)
        self::checkAction( 'playCard' ); 
        
        $player_id = self::getActivePlayerId();
        
        // Add your game logic to play a card there 
        ...
        
        // Notify all players about the card played
        self::notifyAllPlayers( "cardPlayed", clienttranslate( '${player_name} plays ${card_name}' ), array(
            'player_id' => $player_id,
            'player_name' => self::getActivePlayerName(),
            'card_name' => $card_name,
            'card_id' => $card_id
        ) );
          
    }
    
    */


    //////////////////////////////////////////////////////////////////////////////
    //////////// Game state arguments
    ////////////

    /*
        Here, you can create methods defined as "game state arguments" (see "args" property in states.inc.php).
        These methods function is to return some additional information that is specific to the current
        game state.
    */

    /*
    
    Example for game state "MyGameState":
    
    function argMyGameState()
    {
        // Get some values from the current game situation in database...
    
        // return values:
        return array(
            'variable1' => $value1,
            'variable2' => $value2,
            ...
        );
    }    
    */

    //////////////////////////////////////////////////////////////////////////////
    //////////// Game state actions
    ////////////

    /*
        Here, you can create methods defined as "game state actions" (see "action" property in states.inc.php).
        The action method of state X is called everytime the current game state is set to X.
    */

    /*
    
    Example for game state "MyGameState":

    function stMyGameState()
    {
        // Do some stuff ...
        
        // (very often) go to another gamestate
        $this->gamestate->nextState( 'some_gamestate_transition' );
    }    
    */

    //////////////////////////////////////////////////////////////////////////////
    //////////// Zombie
    ////////////

    /*
        zombieTurn:
        
        This method is called each time it is the turn of a player who has quit the game (= "zombie" player).
        You can do whatever you want in order to make sure the turn of this player ends appropriately
        (ex: pass).
        
        Important: your zombie code will be called when the player leaves the game. This action is triggered
        from the main site and propagated to the gameserver from a server, not from a browser.
        As a consequence, there is no current player associated to this action. In your zombieTurn function,
        you must _never_ use getCurrentPlayerId() or getCurrentPlayerName(), otherwise it will fail with a "Not logged" error message. 
    */

    function zombieTurn($state, $active_player) {
        $statename = $state['name'];

        if ($state['type'] === "activeplayer") {
            switch ($statename) {
                default:
                    $this->gamestate->nextState("zombiePass");
                    break;
            }

            return;
        }

        if ($state['type'] === "multipleactiveplayer") {
            // Make sure player is in a non blocking status for role turn
            $this->gamestate->setPlayerNonMultiactive($active_player, '');

            return;
        }

        throw new feException("Zombie mode not supported at this game state: " . $statename);
    }

    ///////////////////////////////////////////////////////////////////////////////////:
    ////////// DB upgrade
    //////////

    /*
        upgradeTableDb:
        
        You don't have to care about this until your game has been published on BGA.
        Once your game is on BGA, this method is called everytime the system detects a game running with your old
        Database scheme.
        In this case, if you change your Database scheme, you just have to apply the needed changes in order to
        update the game database and allow the game to continue to run with your new version.
    
    */

    function upgradeTableDb($from_version) {
        // $from_version is the current version of this game database, in numerical form.
        // For example, if the game was running with a release of your game named "140430-1345",
        // $from_version is equal to 1404301345

        // Example:
        //        if( $from_version <= 1404301345 )
        //        {
        //            // ! important ! Use DBPREFIX_<table_name> for all tables
        //
        //            $sql = "ALTER TABLE DBPREFIX_xxxxxxx ....";
        //            self::applyDbUpgradeToAllDB( $sql );
        //        }
        //        if( $from_version <= 1405061421 )
        //        {
        //            // ! important ! Use DBPREFIX_<table_name> for all tables
        //
        //            $sql = "CREATE TABLE DBPREFIX_xxxxxxx ....";
        //            self::applyDbUpgradeToAllDB( $sql );
        //        }
        //        // Please add your future database scheme changes here
        //
        //


    }

    // Exposing protected method translation
    public static function translate($text) {
        return self::_($text);
    }
}
