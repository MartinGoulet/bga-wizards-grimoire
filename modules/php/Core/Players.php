<?php

namespace WizardsGrimoire\Core;

use WizardsGrimoire\Objects\CardLocation;

/*
 * Players manager : allows to easily access players ...
 *  a player is an instance of Player class
 */

class Players extends \APP_DbObject {

    public static function dealDamage(int $damage, int $opponent_id = -1) {

        if($opponent_id <= 0) {
            $opponent_id = self::getOpponentId();
        }

        $life = self::getPlayerLife($opponent_id);
        $life_remaining = $life - $damage;
        self::setPlayerLife($opponent_id, $life_remaining);

        return $life_remaining;
    }

    public static function getPlayerId() {
        return intval(Game::get()->getGameStateValue(WG_VAR_CURRENT_PLAYER));
    }

    public static function getOpponentId() {
        $player_id = self::getPlayerId();
        return intval(Game::get()->getNextPlayerTable()[$player_id]);
    }

    public static function getPlayerLife(int $player_id) {
        $sql = "SELECT player_score FROM player WHERE player_id = $player_id";
        return intval(self::getUniqueValueFromDB($sql));
    }

    public static function setPlayerLife(int $player_id, int $life) {
        self::DbQuery("UPDATE player SET player_score = $life WHERE player_id = $player_id");
    }

    public static function getPlayersInOrder($player_id = null) {
        $result = [];

        $players = Game::get()->loadPlayersBasicInfos();
        $next_player = Game::get()->getNextPlayerTable();
        if($player_id == null) {
            $player_id = Players::getPlayerId();
        }

        // Check for spectator
        if (!key_exists($player_id, $players)) {
            $player_id = $next_player[0];
        }

        // Build array starting with current player
        for ($i = 0; $i < count($players); $i++) {
            $result[$player_id] = $players[$player_id];
            $player_id = $next_player[$player_id];
        }

        return $result;
    }
}
