<?php

namespace WizardsGrimoire\Core;

use BgaSystemException;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class ManaCard {

    public static function addOnTopOfDeck($card_id) {
        Game::get()->deck_manas->insertCardOnExtremePosition($card_id, CardLocation::Deck(), true);
    }

    public static function addOnTopOfDiscard($card_id) {
        Game::get()->deck_manas->insertCardOnExtremePosition($card_id, CardLocation::Discard(), true);
    }

    public static function addCardsToHand($cards, $player_id = 0) {
        if ($player_id == 0) {
            $player_id = Players::getPlayerId();
        }
        $card_ids = array_values(array_map(function ($card) {
            return $card['id'];
        }, $cards));
        Game::get()->deck_manas->moveCards($card_ids, CardLocation::Hand(), $player_id);
        Notifications::moveManaCard($player_id, $cards);
    }

    public static function addToHand($card_id, $player_id = 0) {
        if ($player_id == 0) {
            $player_id = Players::getPlayerId();
        }
        Game::get()->deck_manas->moveCard($card_id, CardLocation::Hand(), $player_id);
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

    public static function draw($count, $player_id = 0, string $card_name = null) {
        if ($player_id == 0) {
            $player_id = Players::getPlayerId();
        }

        $deck = Game::get()->deck_manas;

        $card_left = $deck->countCardInLocation(CardLocation::Deck());
        if ($card_left >= $count) {
            $mana_cards = $deck->pickCards($count, CardLocation::Deck(), $player_id);
            Notifications::drawManaCards($player_id, $mana_cards, $card_name);
            $result = $mana_cards;
        } else {
            $mana_cards_1 = $deck->pickCards($card_left, CardLocation::Deck(), $player_id);
            Notifications::drawManaCards($player_id, $mana_cards_1, $card_name);

            self::reshuffle();

            $mana_cards_2 = $deck->pickCards($count - $card_left, CardLocation::Deck(), $player_id);
            Notifications::drawManaCards($player_id, $mana_cards_2, $card_name);

            $result = array_merge($mana_cards_1, $mana_cards_2);
        }

        Game::get()->incStat($count, WG_STAT_NBR_MANA_DRAW, $player_id);
        Game::undoSavepoint();

        return $result;
    }

    public static function drawFromManaCoolDown(int $position, int $player_id = 0) {
        if ($player_id == 0) {
            $player_id = Players::getPlayerId();
        }
        $card = Game::get()->deck_manas->pickCardForLocation(
            CardLocation::PlayerManaCoolDown($player_id, $position),
            CardLocation::Hand(),
            $player_id,
        );
        return $card;
    }

    public static function reshuffle() {
        $deck = Game::get()->deck_manas;
        $deck->moveAllCardsInLocation(CardLocation::Discard(), CardLocation::Deck());
        $deck->shuffle(CardLocation::Deck());
        $cards = $deck->getCardsInLocation(CardLocation::Deck());
        $cards = Game::anonynizeCards($cards);
        Notifications::manaDeckShuffle($cards);
    }

    public static function dealFromDeckToManaCoolDown($position, $player_id = 0) {
        if ($player_id == 0) {
            $player_id = Players::getPlayerId();
        }
        $deck = Game::get()->deck_manas;
        if ($deck->countCardInLocation(CardLocation::Deck()) == 0) {
            self::reshuffle();
        }
        $card = $deck->getCardOnTop(CardLocation::Deck());
        $deck->pickCardForLocation(CardLocation::Deck(), "temp");
        $deck->insertCardOnExtremePosition($card['id'], CardLocation::PlayerManaCoolDown($player_id, $position), true);
        Notifications::moveManaCard($player_id, [$card]);
        Events::onAddManaUnderSpell($player_id, $position);
        Game::undoSavepoint();
    }

    public static function discardManaFromSpell(int $position, int $player_id = 0) {
        if ($player_id == 0) {
            $player_id = Players::getPlayerId();
        }
        $card = ManaCard::getOnTopOnManaCoolDown($position);
        if ($card == null) {
            throw new BgaSystemException("No card found under that spell");
        }
        ManaCard::addOnTopOfDiscard($card['id']);
        Notifications::moveManaCard(Players::getPlayerId(), [$card], false);

        if($player_id == Players::getOpponentId()) {
            Game::undoSavepoint();
        }

        Events::onManaDiscarded($card, $position, $player_id);
    }

    public static function get(int $card_id) {
        return Game::get()->deck_manas->getCard($card_id);
    }

    public static function getBasicAttack() {
        $cards = Game::get()->deck_manas->getCardsInLocation(CardLocation::BasicAttack());
        return array_shift($cards);
    }

    public static function getCards($card_ids) {
        return Game::get()->deck_manas->getCards($card_ids);
    }

    public static function getHand(int $player_id = 0) {
        if ($player_id == 0) {
            $player_id = Players::getPlayerId();
        }
        return Game::get()->deck_manas->getCardsInLocation(CardLocation::Hand(), $player_id);
    }

    public static function getHandCount(int $player_id = 0) {
        if ($player_id == 0) {
            $player_id = Players::getPlayerId();
        }
        return Game::get()->deck_manas->countCardInLocation(CardLocation::Hand(), $player_id);
    }

    public static function getMaxValue($cards) {
        if ($cards == null || sizeof($cards) === 0) {
            return 0;
        }
        return max(array_values(array_map(function ($card) {
            return ManaCard::getPower($card);
        }, $cards)));
    }

    public static function getOnTopOnManaCoolDown(int $position, int $player_id = 0) {
        if ($player_id == 0) {
            $player_id = Players::getPlayerId();
        }
        return Game::get()->deck_manas->getCardOnTop(CardLocation::PlayerManaCoolDown($player_id, $position));
    }

    public static function getOnTopOfDeck() {
        return Game::get()->deck_manas->getCardOnTop(CardLocation::Deck());
    }

    public static function getPower($card) {
        $power = intval($card['type']);
        if (Globals::getIsActiveGrowth()) {
            $power++;
        }
        return $power;
    }

    public static function getRevealedMana() {
        return Game::get()->deck_manas->getCardsInLocation(CardLocation::ManaRevelead());
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

    public static function isInReveleadMana(int $card_id) {

        $card = ManaCard::get($card_id);
        if ($card['location'] != CardLocation::ManaRevelead()) {
            $card_loc = $card['location'];
            $card_loc_arg = $card['location_arg'];
            throw new \BgaSystemException("The card $card_id doesn't belong to revealed mana. Card info : $card_loc, $card_loc_arg");
        }

        return $card;
    }

    public static function isOnTopOfSpell($card, int $player_id = 0) {
        if ($player_id == 0) {
            $player_id = Players::getPlayerId();
        }

        for ($i = 1; $i <= 6; $i++) {
            $topCard = Game::get()->deck_manas->getCardOnTop(CardLocation::PlayerManaCoolDown($player_id, $i));

            if ($topCard && $topCard['id'] == $card['id']) {
                return $i;
            }
        }

        throw new \BgaSystemException("Mana card not under a spell card in the repertoire " . $card['id']);
    }

    public static function revealFromDeck($count) {
        $deck = Game::get()->deck_manas;
        $card_left = $deck->countCardInLocation(CardLocation::Deck());


        if ($card_left >= $count) {
            $cards_before = $deck->getCardsOnTop($count, CardLocation::Deck());
            $mana_cards = $deck->pickCardsForLocation($count, CardLocation::Deck(), CardLocation::ManaRevelead());
            Notifications::revealManaCard(Players::getPlayerId(), $mana_cards);
            Notifications::moveManaCard(Players::getPlayerId(), $cards_before, false);
            
            Game::undoSavepoint();
            return $mana_cards;
        } else {
            $cards_before = $deck->getCardsOnTop($count, CardLocation::Deck());
            $mana_cards_1 = $deck->pickCardsForLocation($card_left, CardLocation::Deck(), CardLocation::ManaRevelead());
            Notifications::revealManaCard(Players::getPlayerId(), $mana_cards_1);
            Notifications::moveManaCard(Players::getPlayerId(), $cards_before, false);

            self::reshuffle();

            $cards_before = $deck->getCardsOnTop($count - $card_left, CardLocation::Deck());
            $mana_cards_2 = $deck->pickCardsForLocation($count - $card_left, CardLocation::Deck(), CardLocation::ManaRevelead());
            Notifications::revealManaCard(Players::getPlayerId(), $mana_cards_2);
            Notifications::moveManaCard(Players::getPlayerId(), $cards_before,  false);

            Game::undoSavepoint();
            return array_merge($mana_cards_1, $mana_cards_2);
        }
    }
}
