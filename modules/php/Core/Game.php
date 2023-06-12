<?php

namespace WizardsGrimoire\Core;

use APP_DbObject;
use WizardsGrimoire;
use WizardsGrimoire\Objects\CardLocation;

/*
 * Game: a wrapper over table object to allow more generic modules
 */

class Game extends APP_DbObject {
    public static function get() {
        return WizardsGrimoire::get();
    }

    public static function anonynizeCards($cards, bool $anonymize = true) {
        if (!$anonymize) return $cards;

        $newCards = [];

        foreach ($cards as $card_id => $card) {
            $card['type'] = null;
            $newCards[] = $card;
        }

        return $newCards;
    }

    static function setGlobalVariable(string $name, /*object|array*/ $obj) {
        $jsonObj = json_encode($obj);
        self::DbQuery("INSERT INTO `global_variables`(`name`, `value`)  VALUES ('$name', '$jsonObj') ON DUPLICATE KEY UPDATE `value` = '$jsonObj'");
    }

    static function getGlobalVariable(string $name, $asArray = null) {
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
