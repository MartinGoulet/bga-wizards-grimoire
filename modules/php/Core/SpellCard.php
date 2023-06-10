<?php

namespace WizardsGrimoire\Core;

use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class SpellCard {

    public static function getCard($card_id) {
        return Game::get()->deck_spells->getCard($card_id);
    }

    public static function getCardInfo($card) {
        return Game::get()->card_types[$card['type']];
    }

    static function getCardInRepertoire($position, int $player_id = null) {
        if($player_id == null) {
            $player_id = Players::getPlayerId();
        }
        $cards = Game::get()->deck_spells->getCardsInLocation(CardLocation::PlayerSpellRepertoire($player_id), $position);
        return array_shift($cards);
    }

    /**
     * @return BaseCard
     */
    static function getInstanceOfCard($card) {
        // Get info of the card
        $card_type = Game::get()->card_types[$card['type']];
        // Create the class for the card logic
        $className = "WizardsGrimoire\\Cards\\" . $card_type['class'];
        /** @var BaseCard */
        $cardClass = new $className();
        return $cardClass;
    }
}
