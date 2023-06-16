<?php

namespace WizardsGrimoire\Core;

use APP_DbObject;
use WizardsGrimoire;

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
    
}
