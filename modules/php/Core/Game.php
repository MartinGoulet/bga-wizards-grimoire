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

    public static function getOngoingActiveSpells($player_id) {
        $game = self::get();
        $spells = $game->deck_spells->getCardsInLocation(CardLocation::PlayerSpellRepertoire($player_id));
        $ongoing_active_spell = array_filter($spells, function($card) use($game, $player_id) {
            $card_type = self::getSpellCard($card);
            if($card_type['activation'] == WG_SPELL_ACTIVATION_ONGOING) {
                $count = ManaCard::countOnTopOfManaCoolDown($card['location_arg']);
                if($count > 0) {
                    return true;
                }
            }
            return false;
        });
        return $ongoing_active_spell;
    }

    public static function getDelayedActiveSpells($player_id) {
        $game = self::get();
        $spells = $game->deck_spells->getCardsInLocation(CardLocation::PlayerSpellRepertoire($player_id));
        $ongoing_active_spell = array_filter($spells, function($card) use($game, $player_id) {
            $card_type = self::getSpellCard($card);
            if($card_type['activation'] == WG_SPELL_ACTIVATION_DELAYED) {
                $count = ManaCard::countOnTopOfManaCoolDown($card['location_arg']);
                if($count > 0) {
                    return true;
                }
            }
            return false;
        });
        return $ongoing_active_spell;
    }

    public static function getSpellCard($card) {
        return Game::get()->card_types[$card['type']];
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
