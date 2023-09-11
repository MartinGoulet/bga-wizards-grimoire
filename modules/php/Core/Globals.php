<?php

namespace WizardsGrimoire\Core;

use APP_DbObject;

/*
 * Globals: Access to global variables
 */

class Globals extends APP_DbObject {

    public static function resetOnNewTurn() {
        Globals::setDiscountAttackSpell(0);
        Globals::setDiscountNextSpell(0);
        Globals::setAmnesiaCount(0);
        Globals::setConsecutivelyAttackSpellCount(0);
        Globals::setSpellPlayed(0);
        Globals::setPreviousSpellPlayed(0);
        Globals::setPreviousSpellDamage(0);
        Globals::setInteractionPlayer(0);
        Globals::setCoolDownDelayedSpellIds([]);
    }

    public static function getAmnesiaCount() {
        return intval(Game::get()->getGameStateValue(WG_VAR_AMNESIA));
    }

    public static function incAmnesiaCount() {
        return intval(Game::get()->incGameStateValue(WG_VAR_AMNESIA, 1));
    }

    public static function setAmnesiaCount(int $value) {
        Game::get()->setGameStateValue(WG_VAR_AMNESIA, $value);
    }

    /** @return array */
    public static function getCoolDownDelayedSpellIds() {
        return Globals::get(WG_GV_COOLDOWN_DELAYED_SPELLS, true);
    }

    public static function setCoolDownDelayedSpellIds(array $spell_delayed_ids) {
        return Globals::set(WG_GV_COOLDOWN_DELAYED_SPELLS, $spell_delayed_ids);
    }

    /** @return array */
    public static function getCoolDownOngoingSpellIds() {
        return Globals::get(WG_GV_COOLDOWN_ONGOING_SPELLS, true);
    }

    public static function setCoolDownOngoingSpellIds(array $spell_delayed_ids) {
        return Globals::set(WG_GV_COOLDOWN_ONGOING_SPELLS, $spell_delayed_ids);
    }

    public static function getConsecutivelyAttackSpellCount() {
        return intval(Game::get()->getGameStateValue(WG_VAR_CONSECUTIVELY_ATTACK_SPELL_CAST));
    }

    public static function incConsecutivelyAttackSpellCount() {
        return intval(Game::get()->incGameStateValue(WG_VAR_CONSECUTIVELY_ATTACK_SPELL_CAST, 1));
    }

    public static function setConsecutivelyAttackSpellCount(int $value) {
        Game::get()->setGameStateValue(WG_VAR_CONSECUTIVELY_ATTACK_SPELL_CAST, $value);
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

    public static function getIsActiveBattleVision() {
        return intval(Game::get()->getGameStateValue(WG_VAR_IS_ACTIVE_BATTLE_VISION)) == Players::getOpponentId();
    }
    
    public static function setIsActiveBattleVision(bool $isActive, int $player_id) {
        $value = $isActive ? $player_id : 0;
        Game::get()->setGameStateValue(WG_VAR_IS_ACTIVE_BATTLE_VISION, $value);
    }

    public static function getIsActiveGrowth() {
        return intval(Game::get()->getGameStateValue(WG_VAR_IS_ACTIVE_GROWTH)) == Players::getPlayerId();
    }

    public static function getIsActiveGrowthPlayer() {
        return intval(Game::get()->getGameStateValue(WG_VAR_IS_ACTIVE_GROWTH));
    }

    public static function setIsActiveGrowth(bool $isActive, int $player_id) {
        $value = $isActive ? $player_id : 0;
        Game::get()->setGameStateValue(WG_VAR_IS_ACTIVE_GROWTH, $value);
    }

    public static function getIsActiveLullaby() {
        return intval(Game::get()->getGameStateValue(WG_VAR_IS_ACTIVE_LULLABY)) > 0;
    }

    public static function getIsActiveLullabyPlayer() {
        return intval(Game::get()->getGameStateValue(WG_VAR_IS_ACTIVE_LULLABY));
    }

    public static function setIsActiveLullaby(bool $isActive, int $player_id) {
        $value = $isActive ? $player_id : 0;
        Game::get()->setGameStateValue(WG_VAR_IS_ACTIVE_LULLABY, $value);
    }
    
    public static function getIsActivePowerHungry() {
        return intval(Game::get()->getGameStateValue(WG_VAR_IS_ACTIVE_POWER_HUNGRY)) > 0;
    }

    public static function getIsActivePowerHungryPlayer() {
        return intval(Game::get()->getGameStateValue(WG_VAR_IS_ACTIVE_POWER_HUNGRY));
    }
    
    public static function setIsActivePowerHungry(bool $isActive, int $player_id) {
        $value = $isActive ? $player_id : 0;
        Game::get()->setGameStateValue(WG_VAR_IS_ACTIVE_POWER_HUNGRY, $value);
    }

    public static function getIsActivePuppetmaster() {
        return intval(Game::get()->getGameStateValue(WG_VAR_IS_ACTIVE_PUPPETMASTER)) == Players::getOpponentId();
    }

    public static function setIsActivePuppetmaster(bool $isActive, int $player_id) {
        $value = $isActive ? $player_id : 0;
        Game::get()->setGameStateValue(WG_VAR_IS_ACTIVE_PUPPETMASTER, $value);
    }

    public static function getIsActiveSecretOath() {
        return intval(Game::get()->getGameStateValue(WG_VAR_IS_ACTIVE_SECRET_OATH)) > 0;
    }

    public static function getIsActiveSecretOathPlayer() {
        return intval(Game::get()->getGameStateValue(WG_VAR_IS_ACTIVE_SECRET_OATH));
    }

    public static function setIsActiveSecretOath(bool $isActive, int $player_id) {
        $value = $isActive ? $player_id : 0;
        Game::get()->setGameStateValue(WG_VAR_IS_ACTIVE_SECRET_OATH, $value);
    }

    public static function getCurrentBasicAttackPower() {
        return intval(Game::get()->getGameStateValue(WG_VAR_CURRENT_BASIC_ATTACK_POWER));
    }

    public static function setCurrentBasicAttackPower(int $value) {
        Game::get()->setGameStateValue(WG_VAR_CURRENT_BASIC_ATTACK_POWER, $value);
    }

    public static function getPlayerTurn() {
        return intval(Game::get()->getGameStateValue(WG_VAR_PLAYER_TURN));
    }

    public static function setPlayerTurn(int $value) {
        Game::get()->setGameStateValue(WG_VAR_PLAYER_TURN, $value);
    }

    public static function getPreviousBasicAttackPower() {
        return intval(Game::get()->getGameStateValue(WG_VAR_PREVIOUS_BASIC_ATTACK_POWER));
    }

    public static function setPreviousBasicAttackPower(int $value) {
        Game::get()->setGameStateValue(WG_VAR_PREVIOUS_BASIC_ATTACK_POWER, $value);
    }

    public static function getLastBasicAttackDamage() {
        $value = self::get(WG_VAR_LAST_BASIC_ATTACK_DAMAGE, true);
        if($value == null) {
            return self::getPreviousBasicAttackPower();
        } else {
            return intval($value['dmg']);
        }
    }

    public static function setLastBasicAttackDamage(int $value) {
        self::set(WG_VAR_LAST_BASIC_ATTACK_DAMAGE, ['dmg' => $value]);
    }

    public static function getPreviousSpellDamage() {
        return intval(Game::get()->getGameStateValue(WG_VAR_PREVIOUS_SPELL_DAMAGE));
    }

    public static function setPreviousSpellDamage(int $damage) {
        Game::get()->setGameStateValue(WG_VAR_PREVIOUS_SPELL_DAMAGE, $damage);
    }

    public static function getPreviousSpellPlayed() {
        return intval(Game::get()->getGameStateValue(WG_VAR_PREVIOUS_SPELL_PLAYED));
    }

    public static function setPreviousSpellPlayed(int $card_id) {
        Game::get()->setGameStateValue(WG_VAR_PREVIOUS_SPELL_PLAYED, $card_id);
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
