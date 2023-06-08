<?php

namespace WizardsGrimoire\Core;

use BgaSystemException;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class Assert {

    public static function hasManaCardUnderSpell($deck_position, $player_id) {
        $card = Game::get()->deck_manas->getCardOnTop(CardLocation::PlayerManaCoolDown($player_id, $deck_position));

        if ($card == null) {
            throw new BgaSystemException("Card not found under the spell at position " . $deck_position);
        }

        return $card;
    }

    public static function isCardInRepertoire($card_id, $player_id) {
        $card = Game::get()->deck_spells->getCard($card_id);

        if ($card['location'] != CardLocation::PlayerSpellRepertoire($player_id)) {
            throw new \BgaUserException(Game::get()->_("You don't own the card"));
        }

        return $card;
    }

    public static function isCardInHand(int $card_id, int $player_id = 0) {
        if($player_id == 0) {
            $player_id = Players::getPlayerId();
        }

        $card = Game::get()->deck_manas->getCard($card_id);

        if ($card['location'] !== CardLocation::Hand() || intval($card['location_arg']) !== $player_id) {
            throw new \BgaUserException(Game::get()->_("You don't own the card"));
        }

        return $card;
    }

    public static function isManaCardOnTopOfSpellCard($card, $player_id) {

        for ($i = 1; $i <= 6; $i++) {
            $isOnTop =
                $card['location'] == CardLocation::PlayerManaCoolDown($player_id, $i) &&
                $card['location_arg'] == 1;

            if ($isOnTop) {
                return true;
            }
        }

        throw new \BgaSystemException("Mana card not under a spell card in the repertoire " . $card['id']);
    }
}
