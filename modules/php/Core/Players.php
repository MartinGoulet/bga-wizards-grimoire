<?php

namespace WizardsGrimoire\Core;


/*
 * Players manager : allows to easily access players ...
 *  a player is an instance of Player class
 */

class Players extends \APP_DbObject {

    public static function getOpponentId() {
        $player_id = Game::get()->getActivePlayerId();
        $opponent_id = Game::get()->getNextPlayerTable()[$player_id];
        return $opponent_id;
    }

    public static function getPlayerLife(int $player_id) {
        $sql = "SELECT player_score FROM player WHERE player_id = $player_id";
        return intval(self::getUniqueValueFromDB($sql));
    }

    public static function setPlayerLife(int $player_id, int $life) {
        self::DbQuery("UPDATE player SET player_score = $life WHERE player_id = $player_id");
    }
}
