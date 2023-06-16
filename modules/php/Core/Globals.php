<?php

namespace WizardsGrimoire\Core;

use APP_DbObject;
use WizardsGrimoire;
use WizardsGrimoire\Objects\CardLocation;

/*
 * Globals: Access to global variables
 */

class Globals extends APP_DbObject {


    /** @return array */
    public static function getCoolDownDelayedSpell() {
        return Globals::get(WG_GV_COOLDOWN_DELAYED_SPELLS, true);
    }

    public static function setCoolDownDelayedSpell(array $spell_delayed) {
        return Globals::set(WG_GV_COOLDOWN_DELAYED_SPELLS, $spell_delayed);
    }

    public static function getDiscountAttackSpell() {
        return intval(Game::get()->getGameStateValue(WG_VAR_DISCOUNT_ATTACK_SPELL));
    }

    public static function setDiscountAttackSpell(int $value) {
        Game::get()->setGameStateValue(WG_VAR_DISCOUNT_ATTACK_SPELL, $value);
    }

    public static function getDiscountNextSpell() {
        return intval(Game::get()->getGameStateValue(WG_VAR_DISCOUNT_NEXT_SPELL));
    }

    public static function setDiscountNextSpell(int $value) {
        Game::get()->setGameStateValue(WG_VAR_DISCOUNT_NEXT_SPELL, $value);
    }

    public static function getInteractionPlayer() {
        return intval(Game::get()->getGameStateValue(WG_VAR_INTERACTION_PLAYER));
    }

    public static function setInteractionPlayer(int $player_id) {
        Game::get()->setGameStateValue(WG_VAR_INTERACTION_PLAYER, $player_id);
    }

    public static function getSkipInteraction() {
        return intval(Game::get()->getGameStateValue(WG_VAR_SKIP_INTERACTION)) == 1;
    }

    public static function setSkipInteraction(bool $skip) {
        $value = $skip ? 1 : 0;
        Game::get()->setGameStateValue(WG_VAR_SKIP_INTERACTION, $value);
    }

    public static function getSpellPlayed() {
        return intval(Game::get()->getGameStateValue(WG_VAR_SPELL_PLAYED));
    }

    public static function setSpellPlayed(int $card_id) {
        Game::get()->setGameStateValue(WG_VAR_SPELL_PLAYED, $card_id);
    }

    /*************************
     **** GENERIC METHODS ****
     *************************/

    private static function set(string $name, /*object|array*/ $obj) {
        $jsonObj = json_encode($obj);
        self::DbQuery("INSERT INTO `global_variables`(`name`, `value`)  VALUES ('$name', '$jsonObj') ON DUPLICATE KEY UPDATE `value` = '$jsonObj'");
    }

    private static function get(string $name, $asArray = null) {
        /** @var string */
        $json_obj = self::getUniqueValueFromDB("SELECT `value` FROM `global_variables` where `name` = '$name'");
        if ($json_obj) {
            $object = json_decode($json_obj, $asArray);
            return $object;
        } else {
            return null;
        }
    }
}
