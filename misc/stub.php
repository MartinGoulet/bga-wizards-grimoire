<?php

define("APP_GAMEMODULE_PATH", "");

function clienttranslate($message)
{
}

function totranslate($message)
{
}


/**
 * For Intellisense Only (VS Code)
 */
class Table
{
    public $gamestate;

    /**
     * Default Constructor
     */
    function __construct()
    {
        $this->gamestate = new Gamestate();
    }

    //////////////////////////////////////////////////////////////////////////////
    //////////// Game
    //////////// 

    /**
     * Get custom module from Board Game Arena
     * @return Card Instance of the module asked
     */
    static function getNew($module_name)
    {
        // return new Card();
    }

    static function getGameinfos()
    {
    }

    static function _($message)
    {
    }

    //////////////////////////////////////////////////////////////////////////////
    //////////// Accessing player information
    //////////// 

    /**
     * Returns the number of players playing at the table
     * Note: doesn't work in setupNewGame (use count($players) instead).
     */
    static function getPlayersNumber()
    {
        return 1;
    }

    /**
     * Return the "active_player" id
     * Note: it does NOT mean that this player is active right now, because state type could be "game" or "multipleactiveplayer"
     * Note: avoid using this method in a "multipleactiveplayer" state because it does not mean anything.
     */
    static function getActivePlayerId()
    {
        return 0;
    }

    /**
     * Get the "active_player" name
     * Note: avoid using this method in a "multiplayer" state because it does not mean anything.
     */
    static function getActivePlayerName()
    {
    }

    /**
     * Get the name by id
     */
    static function getPlayerNameById($player_id) 
    {
        return "";
    }

    /**
     * Get an associative array with generic data about players (ie: not game specific data).
     * The key of the associative array is the player id. The returned table is cached, so ok to call multiple times without performance concerns.
     * The content of each value is:
     * * player_name - the name of the player
     * * player_color (ex: ff0000) - the color code of the player
     * * player_no - the position of the player at the start of the game in natural table order, i.e. 1,2,3
     */
    static function loadPlayersBasicInfos()
    {
        return array();
    }

    /**
     * Get the "current_player". The current player is the one from which the action originated (the one who sent the request).
     * @return int
     */
    protected static function getCurrentPlayerId()
    {
    }

    /**
     * Get the "current_player" name
     */
    function getCurrentPlayerName()
    {
    }

    /**
     * Get the "current_player" color
     */
    function getCurrentPlayerColor()
    {
    }

    /**
     * Check the "current_player" zombie status. If true, player is zombie, i.e. left or was kicked out of the game.
     */
    function isCurrentPlayerZombie()
    {
    }


    //////////////////////////////////////////////////////////////////////////////
    //////////// Accessing the database
    //////////// 


    /**
     * This is the generic method to access the database.
     * It can execute any type of SELECT/UPDATE/DELETE/REPLACE/INSERT query on the database.
     * You should use it for UPDATE/DELETE/REPLACE/INSERT queries. 
     * For SELECT queries, the specialized methods below are much better.
     */
    static function DbQuery($sql)
    {
    }

    /**
     * Returns a unique value from DB or null if no value is found.
     * @param string $sql must be a SELECT query.
     * @throws Exception Raise an exception if more than 1 row is returned.
     */
    static function getUniqueValueFromDB($sql)
    {
    }

    /**
     * Returns an associative array of rows for a sql SELECT query.
     * The key of the resulting associative array is the first field specified in the SELECT query.
     * The value of the resulting associative array is an associative array with all the field specified in the SELECT query and associated values.
     * First column must be a primary or alternate key.
     * The resulting collection can be empty.
     * If you specified $bSingleValue=true and if your SQL query request 2 fields A and B, the method returns an associative array "A=>B"
     * @return array List of values
     */
    static function getCollectionFromDB($sql, $bSingleValue = false)
    {
    }

    /**
     * Returns one row for the sql SELECT query as an associative array or null if there is no result
     * Raise an exception if the query return more than one row
     */
    static function getObjectFromDB($sql)
    {
    }

    /**
     * Return an array of rows for a sql SELECT query.
     * the result if the same than "getCollectionFromDB" except that the result is a simple array (and not an associative array).
     * The result can be empty.
     * If you specified $bUniqueValue=true and if your SQL query request 1 field, the method returns directly an array of values.
     * @return array List of values
     */
    static function getObjectListFromDB($sql, $bUniqueValue = false)
    {
    }

    //////////////////////////////////////////////////////////////////////////////
    //////////// Use globals
    //////////// 

    /**
     * This method should be located at the beginning of yourgamename.php. 
     * This is where you define the globals used in your game logic, by assigning them IDs.
     *
     * You can define up to 79 globals, with IDs from 10 to 89 (inclusive). You must not use globals outside this range, 
     * as those values are used by other components of the framework.
     */
    static function initGameStateLabels($array)
    {
    }

    /**
     * Initialize your global value. Must be called before any use of your global, so you should call this method from your "setupNewGame" method.
     */
    static function setGameStateInitialValue($value_label, $value_value)
    {
    }

    /**
     * Retrieve the current value of a global.
     */
    static function getGameStateValue($value_label)
    {
    }

    /**
     * Set the current value of a global.
     */
    static function setGameStateValue($value_label, $value_value)
    {
    }


    /**
     * Increment the current value of a global. If increment is negative, decrement the value of the global.
     * @return int Return the final value of the global.
     */
    static function incGameStateValue($value_label, $increment)
    {
        return 0;
    }

    //////////////////////////////////////////////////////////////////////////////
    //////////// Game states and active players
    ////////////

    /**
     * Make the next player active in the natural player order.
     * Note: you CANNOT use this method in a "activeplayer" or "multipleactiveplayer" state. You must use a "game" type game state for this.
     */
    protected function activeNextPlayer()
    {
    }

    /**
     * Make the previous player active (in the natural player order).
     * Note: you CANNOT use this method in a "activeplayer" or "multipleactiveplayer" state. You must use a "game" type game state for this.
     */
    function activePrevPlayer()
    {
    }

    /**
     * Give standard extra time to this player.
     * Standard extra time depends on the speed of the game (small with "slow" game option, bigger with other options).
     * You can also specify an exact time to add, in seconds, with the "specified_time" argument (rarely used).
     */
    protected static function giveExtraTime($player_id, $specific_time=null )
    {
    }


    //////////////////////////////////////////////////////////////////////////////
    //////////// Players turn order
    ////////////

    /**
     * Return an associative array which associate each player with the next player around the table.
     * In addition, key 0 is associated to the first player to play.
     * Example: if three player with ID 1, 2 and 3 are around the table, in this order, the method returns:
     */
    static function getNextPlayerTable()
    {
        return array();
    }

    /**
     * Same as getNextPlayerTable, but the associative array associate the previous player around the table. 
     * Here seems also the "0" missing.
     */
    static function getPrevPlayerTable()
    {
        return array();
    }

    /**
     * Get player playing after given player in natural playing order.
     */
    static function getPlayerAfter($player_id)
    {
    }

    /**
     * Get player playing before given player in natural playing order.
     */
    static function getPlayerBefore($player_id)
    {
    }

    //////////////////////////////////////////////////////////////////////////////
    //////////// Notify players
    ////////////

    static function notifyAllPlayers($notification_type, $notification_log, $notification_args)
    {
    }

    static function notifyPlayer($player_id, $notification_type, $notification_log, $notification_args)
    {
    }

    //////////////////////////////////////////////////////////////////////////////
    //////////// About random and randomness
    ////////////

    /**
     * This is a BGA framework function that provides you a random number between "min" and "max" (inclusive), 
     * using the best available random method available on the system.
     */
    function bga_rand($min, $max)
    {
    }

    //////////////////////////////////////////////////////////////////////////////
    //////////// Game statistics
    ////////////  

    /**
     * Create a statistic entry with a default value.
     * 
     * This method must be called for each statistic of your game, in your setupNewGame method. 
     * If you neglect to call this for a statistic, and also do not update the value during the course of a c
     * ertain game using setStat or incStat, the value of the stat will be undefined rather than 0. T
     * his will result in it being ignored at the end of the game, as if it didn't apply to that particular game, 
     * and excluded from cumulative statistics.
     * 
     * @param string $table_or_player must be set to "table" if this is a table statistic, or "player" if this is a player statistic.
     * @param string $name is the name of your statistic, as it has been defined in your stats.inc.php file.
     * @param string $value is the initial value of the statistic. If this is a player statistic and if the player is not specified by "$player_id" argument, the value is set for ALL players.
     */
    static function initStat($table_or_player, $name, $value, $player_id = null)
    {
    }

    /**
     * Set a statistic $name to $value.
     */
    static function setStat($value, $name, $player_id = null)
    {
    }

    /**
     * Increment (or decrement) specified statistic value by $delta value. Same behavior as setStat function.
     */
    static function incStat($delta, $name, $player_id = null)
    {
    }

    /**
     * Return the value of statistic specified by $name. Useful when creating derivative statistics such as average.
     */
    static function getStat($name, $player_id = null)
    {
        return 0;
    }


    //////////////////////////////////////////////////////////////////////////////
    //////////// Player informations
    ////////////

    static function reloadPlayersBasicInfos()
    {
    }

    //////////////////////////////////////////////////////////////////////////////
    //////////// Player color preferences
    ////////////

    /**
     * @param array $players List of players
     * @param array $colors List of colors used by the game
     */
    static function reattributeColorsBasedOnPreferences($players, $colors)
    {
    }

    //////////////////////////////////////////////////////////////////////////////
    //////////// States functions
    ////////////

    /**
     * Check if the current player can perform a specific action in the current game state, and optionally throw an exception if they can't.
     * The action is valid if it is listed in the "possibleactions" array for the current game state (see game state description).
     */
    static function checkAction($actionName, $bThrowException = true)
    {
    }
}

class Gamestate
{
    /**
     * You can call this method to make any player active.
     * Note: you CANNOT use this method in a "activeplayer" or "multipleactiveplayer" state. You must use a "game" type game state for this.
     */
    function changeActivePlayer($player_id)
    {
    }

    /**
     * All playing players are made active. Update notification is sent to all players (triggers onUpdateActionButtons).
     * Usually, you use this method at the beginning (ex: "st" action method) of a multiplayer game state when all players 
     * have to do some action. Do not use method if you going to do some more chages in active player list, 
     * i.e. if you want to take away multi-active right after, use setPlayersMultiactive instead.
     */
    function setAllPlayersMultiactive()
    {
    }

    /**
     * This works exactly like "checkAction" (above), except that it does NOT check if the current player is active.
     */
    function checkPossibleAction($action)
    {
    }

    /**
     * Change current state to a new state. Important: the $transition parameter is the name of the transition, and NOT the name of the target game state, 
     * see Your game state machine: states.inc.php for more information about states
     */
    function nextState($transition = null)
    {
    }

    /**
     * Get an associative array of current game state attributes, see Your game state machine: states.inc.php for state attributes.
     */
    function state()
    {
        return array(
            "name" => ""
        );
    }

    /**
     * Retrieve the list of the active player at any time.
     */
    function getActivePlayerList()
    {
    }

    /**
     * All playing players are made inactive. Transition to next state
     */
    function setAllPlayersNonMultiactive()
    {
    }

    /**
     * Make a specific list of players active during a multiactive gamestate. Update notification is sent to all players who's state changed.
     * 
     * In case "players" is empty, the method trigger the "next_state" transition to go to the next game state.
     * 
     * @param array $players is the array of player id that should be made active
     * @param string $next_state name of the next state
     * @param bool $bExclusive If "exclusive" parameter is not set or false it doesn't deactivate other previously active players. If its set to true, the players who will be multiactive at the end are only these in "$players" array
     * 
     * @return bool true if state transition happened, false otherwise
     */
    function setPlayersMultiactive($players, $next_state, $bExclusive = false)
    {
    }

    /**
     * During a multiactive game state, make the specified player inactive.
     */
    function setPlayerNonMultiactive($player_id, $next_state)
    {
    }
}

class BgaUserException extends feException
{
    function __construct($message)
    {
        parent::__construct($message);
    }
}

class feException extends Exception
{
    function __construct($message)
    {
        parent::__construct($message);
    }
}


class BgaSystemException extends feException
{
    function __construct($message)
    {
        parent::__construct($message);
    }
}

class Deck
{
    //////////////////////////////////////////////////////////////////////////////
    //////////// Initializing Deck component
    ////////////

    /**
     * Initialize the Deck component.
     * 
     * @param string $table_name name of the DB table used by this Deck component.
     */
    function init($table_name)
    {
    }

    /**
     * Create card items in your deck component. Usually, all card items are created once, during the setup phase of the game.
     */
    function createCards($cards, $location = 'deck', $location_arg = null)
    {
    }

    //////////////////////////////////////////////////////////////////////////////
    //////////// Picking cards
    ////////////

    /**
     * Pick a card from a "pile" location (ex: "deck") and place it in the "hand" of specified player.
     * 
     * This method supports auto-reshuffle (see "auto-reshuffle" below).
     * 
     * @return array Return the card picked or "null" if there are no more card in given location.
     */
    function pickCard($location, $player_id)
    {
    }

    /**
     * Pick "$nbr" cards from a "pile" location (ex: "deck") and place them in the "hand" of specified player.
     * 
     * This method supports auto-reshuffle (see "auto-reshuffle" below). In case there are not enough cards in the pile, 
     * all remaining cards are picked first, then the auto-reshuffle is triggered, then the other cards are picked.
     * 
     * @return array Return an array with the cards picked (indexed by the card ID), or "null" if there are no more card in given location.
     */
    function pickCards($nbr, $location, $player_id)
    {
    }

    /**
     * This method is similar to 'pickCard', except that you can pick a card for any sort of location and not only the "hand" location.
     * 
     * This method supports auto-reshuffle (see "auto-reshuffle" below).
     * 
     * @param string $from_location is the "pile" style location from where you are picking a card.
     * @param string $to_location is the location where you will place the card picked.
     * @param int $location_arg the card picked will be set with this "location_arg"
     */
    function pickCardForLocation($from_location, $to_location, $location_arg = 0)
    {
    }

    /**
     * This method is similar to 'pickCards', except that you can pick cards for any sort of location and not only the "hand" location.
     * @return array
     */
    function pickCardsForLocation($nbr, $from_location, $to_location, $location_arg = 0, $no_deck_reform = false)
    {
        return array();
    }

    //////////////////////////////////////////////////////////////////////////////
    //////////// Moving cards
    ////////////

    /**
     * Move the specific card to given location
     * 
     * @param int $card_id ID of the card to move.
     * @param string $location where to move the card.
     * @param int $location_arg if specified, location_arg where to move the card
     */
    function moveCard($card_id, $location, $location_arg = 0)
    {
    }

    /**
     * Move the specific cards to given location.
     * 
     * @param array $cards an array of IDs of cards to move.
     * @param string $location where to move the card.
     * @param int $location_arg if specified, location_arg where to move the card
     */
    function moveCards($cards, $location, $location_arg = 0)
    {
    }

    /**
     * Move a card to a specific "pile" location where card are ordered.
     */
    function insertCard($card_id, $location, $location_arg)
    {
    }

    /**
     * Move a card on top or at bottom of given "pile" type location.
     */
    function insertCardOnExtremePosition($card_id, $location, $bOnTop)
    {
    }

    /**
     * Move all cards in specified "from" location to given location.
     * @param $from_location where to take the cards. If null, cards from all locations will be move.
     * @param $to_location where to put the cards
     * @param $from_location_arg (optional): if specified, only cards with given "location_arg" are moved.
     * @param $to_location_arg (optional) : if specified, cards moved "location_arg" is set to given value.
     */
    function moveAllCardsInLocation($from_location, $to_location, $from_location_arg = null, $to_location_arg = 0)
    {
    }

    /**
     * Move all cards in specified "from" location to given "to" location. 
     * This method does not modify the "location_arg" of cards.
     * @param $from_location where to take the cards. If null, cards from all locations will be move.
     * @param $to_location where to put the cards
     */
    function moveAllCardsInLocationKeepOrder($from_location, $to_location)
    {
    }

    /**
     * Move specified card at the top of the "discard" location.
     * Note: this is an alias for: insertCardOnExtremePosition( $card_id, "discard", true )
     */
    function playCard($card_id)
    {
    }

    //////////////////////////////////////////////////////////////////////////////
    //////////// Get cards informations
    ////////////

    /**
     * Get specific card information.
     * @return array if this card is not found
     */
    function getCard($card_id)
    {
    }

    /**
     * Get specific cards information.
     * If some cards are not found or if some card IDs are specified multiple times, the method throws an (unexpected) Exception.
     */
    function getCards($cards_array)
    {
        return array();
    }

    /**
     * Get all cards in specific location, as an array. Return an empty array if the location is empty.
     * @param string $location the location where to get the cards.
     * @param int $location_arg (optional): if specified, return only cards with the specified "location_arg".
     * @param string $order_by (optional): if specified, returned cards are ordered by the given database field. Example: "card_id" or "card_type".
     */
    function getCardsInLocation($location, $location_arg = null, $order_by = null)
    {
        return array();
    }

    /**
     * Return the number of cards in specified location.
     * @param string $location the location where to get the cards.
     * @param int $location_arg (optional): if specified, return only cards with the specified "location_arg".
     */
    function countCardInLocation($location, $location_arg = null)
    {
        return 1;
    }

    /**
     * Return the number of cards in each location of the game.
     * The method returns an associative array with the format "location" => "number of cards".
     */
    function countCardsInLocations()
    {
        return 1;
    }

    /**
     * Return the number of cards in each "location_arg" for the given location.
     * The method returns an associative array with the format "location_arg" => "number of cards".
     */
    function countCardsByLocationArgs($location)
    {
        return array(123456 => 1);
    }

    /**
     * Get all cards in given player hand.
     * Note: This is an alias for: getCardsInLocation( "hand", $player_id )
     */
    function getPlayerHand($player_id)
    {
        return $this->getCardsInLocation("hand", $player_id);
    }

    /**
     * Get the card on top of the given ("pile" style) location, or null if the location is empty.
     * Note that the card pile won't be "auto-reshuffled" if there is no more card available.
     */
    function getCardOnTop($location)
    {
        return array();
    }

    /**
     * Get the "$nbr" cards on top of the given ("pile" style) location.
     * The method return an array with at most "$nbr" elements (or a void array if there is no card in this location).
     * Note that the card pile won't be "auto-reshuffled" if there is not enough cards available.
     */
    function getCardsOnTop($nbr, $location)
    {
        return array();
    }

    /**
     * (rarely used)
     * Get the position of cards at the top of the given location / at the bottom of the given location.
     * @param bool $bGetMax True return top card, False return bottom card
     */
    function getExtremePosition($bGetMax, $location)
    {
    }

    /**
     * Get all cards of a specific type (rarely used).
     * @param string $type Type of cards
     * @param int $type_arg if specified, return only cards with the specified "type_arg".
     * @return array Return an array of cards, or an empty array if there is no cards of the specified type.
     */
    function getCardsOfType($type, $type_arg = null)
    {
    }

    /**
     * Get all cards of a specific type in a specific location (rarely used).
     * @param string $type the type of cards
     * @param int $type_arg if specified, return only cards with the specified "type_arg".
     * @param string $location the location where to get the cards.
     * @param int $location_arg if specified, return only cards with the specified "location_arg".
     * @return array Return an array of cards, or an empty array if there is no cards of the specified type.
     */
    function getCardsOfTypeInLocation($type, $type_arg = null, $location, $location_arg = null)
    {
    }

    //////////////////////////////////////////////////////////////////////////////
    //////////// Get cards informations
    ////////////

    /**
     * Shuffle all cards in specific location.
     * Shuffle only works on locations where cards are on a "pile" (ex: "deck").
     * Please note that all "location_arg" will be reset to reflect the new order of the cards in the pile.
     */
    function shuffle($location)
    {
    }

    /**
     * To enable auto-reshuffle you must do "$this->cards->autoreshuffle = true" during the setup of the component 
     * (often in the _construct function when you init() the Deck object).
     * 
     * @var bool autoreshuffle
     */
    public $autoreshuffle;
}
