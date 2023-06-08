<?php

namespace WizardsGrimoire\Core;

use WizardsGrimoire;
use WizardsGrimoire\Objects\CardLocation;

/*
 * Game: a wrapper over table object to allow more generic modules
 */

class Game {
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
                $count = $game->deck_manas->countCardInLocation(CardLocation::PlayerManaCoolDown($player_id, $card['location_arg']));
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
                $count = $game->deck_manas->countCardInLocation(CardLocation::PlayerManaCoolDown($player_id, $card['location_arg']));
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

    public static function getMaxManaCardValue($cards) {
        if($cards == null || sizeof($cards) === 0) {
            return 0;
        }
        return max(array_values(array_map(function ($card) {
            return $card['type'];
        }, $cards)));
    }
}
