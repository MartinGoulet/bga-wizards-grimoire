<?php

namespace WizardsGrimoire\Core;

use BgaSystemException;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class ManaCard {

    public static function addOnTopOfDiscard($card_id) {
        Game::get()->deck_manas->insertCardOnExtremePosition($card_id, CardLocation::Discard(), true);
    }

    public static function addOnTopOfManaCoolDown(int $card_id, int $position, int $player_id = 0) {
        if ($player_id == 0) {
            $player_id = Players::getPlayerId();
        }
        Game::get()->deck_manas->insertCardOnExtremePosition(
            $card_id,
            CardLocation::PlayerManaCoolDown($player_id, $position),
            true
        );
    }

    public static function countOnTopOfManaCoolDown(int $position, int $player_id = 0) {
        if ($player_id == 0) {
            $player_id = Players::getPlayerId();
        }
        return Game::get()->deck_manas->countCardInLocation(CardLocation::PlayerManaCoolDown($player_id, $position));
    }

    public static function Draw($count, $player_id = 0) {
        if ($player_id == 0) {
            $player_id = Players::getPlayerId();
        }

        $deck = Game::get()->deck_manas;

        $card_left = $deck->countCardInLocation(CardLocation::Deck());
        if ($card_left >= $count) {
            $mana_cards = $deck->pickCards($count, CardLocation::Deck(), $player_id);
            Notifications::drawManaCards($player_id, $mana_cards);
            return $mana_cards;
        } else {
            $mana_cards_1 = $deck->pickCards($card_left, CardLocation::Deck(), $player_id);
            Notifications::drawManaCards($player_id, $mana_cards_1);

            $deck->moveAllCardsInLocation(CardLocation::Discard(), CardLocation::Deck());
            $deck->shuffle(CardLocation::Deck());
            $cards = $deck->getCardsInLocation(CardLocation::Deck());
            $cards = Game::anonynizeCards($cards);
            Notifications::manaDeckShuffle($cards);

            $mana_cards_2 = $deck->pickCards($count - $card_left, CardLocation::Deck(), $player_id);
            Notifications::drawManaCards($player_id, $mana_cards_2);

            return array_merge($mana_cards_1, $mana_cards_2);
        }
    }

    public static function get(int $card_id) {
        return Game::get()->deck_manas->getCard($card_id);
    }

    public static function getCards($card_ids) {
        return Game::get()->deck_manas->getCards($card_ids);
    }

    public static function getHand(int $player_id = 0) {
        if($player_id == 0) {
            $player_id = Players::getPlayerId();
        }
        return Game::get()->deck_manas->getCardsInLocation(CardLocation::Hand(), $player_id);
    }

    public static function getHandCount(int $player_id = 0) {
        if($player_id == 0) {
            $player_id = Players::getPlayerId();
        }
        return Game::get()->deck_manas->countCardInLocation(CardLocation::Hand(), $player_id);
    }

    public static function getMaxValue($cards) {
        if($cards == null || sizeof($cards) === 0) {
            return 0;
        }
        return max(array_values(array_map(function ($card) {
            return $card['type'];
        }, $cards)));
    }

    public static function getOnTopOnManaCoolDown(int $position, int $player_id = 0) {
        if ($player_id == 0) {
            $player_id = Players::getPlayerId();
        }
        return Game::get()->deck_manas->getCardOnTop(CardLocation::PlayerManaCoolDown($player_id, $position));
    }

    public static function hasUnderSpell(int $deck_position, int $player_id = 0) {
        if ($player_id == 0) {
            $player_id = Players::getPlayerId();
        }
        $card = ManaCard::getOnTopOnManaCoolDown($deck_position, $player_id);

        if ($card == null) {
            throw new BgaSystemException("Mana card not found under the spell at position " . $deck_position);
        }

        return $card;
    }

    public static function isInDiscard(int $card_id) {
        $card = ManaCard::get($card_id);
        if ($card['location'] != CardLocation::Discard()) {
            $card_loc = $card['location'];
            $card_loc_arg = $card['location_arg'];
            throw new \BgaSystemException("The card $card_id doesn't belong to discard. Card info : $card_loc, $card_loc_arg");
        }

        return $card;
    }

    public static function isInHand(int $card_id, int $player_id = 0) {
        if ($player_id == 0) {
            $player_id = Players::getPlayerId();
        }

        $card = ManaCard::get($card_id);
        if ($card['location'] != CardLocation::Hand() || intval($card['location_arg']) != $player_id) {
            $card_loc = $card['location'];
            $card_loc_arg = $card['location_arg'];
            throw new \BgaSystemException("The card $card_id doesn't belong to $player_id. Card info : $card_loc, $card_loc_arg");
        }

        return $card;
    }

    public static function isOnTopOfSpell($card, $player_id) {

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
