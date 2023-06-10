<?php

namespace WizardsGrimoire\Core;

use BgaSystemException;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class ManaCard {

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
}
