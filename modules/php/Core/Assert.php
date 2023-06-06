<?php

namespace WizardsGrimoire\Core;

use WizardsGrimoire\Objects\CardLocation;

class Assert {

    public static function isCardInCurrentPlayerRepertoire($card_id, $player_id) {
        $card = Game::get()->deck_spells->getCard($card_id);

        if ($card['location'] != CardLocation::PlayerSpellRepertoire($player_id)) {
            throw new \BgaUserException(Game::get()->_("You don't own the card"));
        }

        return $card;
    }

    public static function isCardInCurrentPlayerHand(int $card_id, int $player_id) {
        $card = Game::get()->deck_manas->getCard($card_id);

        if ($card['location'] !== CardLocation::Hand() || intval($card['location_arg']) !== $player_id) {
            throw new \BgaUserException(Game::get()->_("You don't own the card"));
        }

        return $card;
    }
}
