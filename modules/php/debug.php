<?php

namespace WizardsGrimoire;

use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

trait DebugTrait {

    public function refillSpells() {
        $cards = Game::get()->deck_spells->getCardsInLocation(CardLocation::SpellSlot());

        foreach ($cards as $card_id => $card) {
            Game::get()->deck_spells->insertCardOnExtremePosition($card['id'], CardLocation::Discard(), true);
            Game::get()->deck_spells->pickCardForLocation(
                CardLocation::Deck(),
                CardLocation::SpellSlot(),
                $card['location_arg'],
            );
        }
    }

    public function drawManyCards($count) {
        ManaCard::draw($count);
    }

    public function resetLife() {
        Players::setPlayerLife(Players::getPlayerId(), 1);
        Players::setPlayerLife(Players::getOpponentId(), 1);
    }

    public function shuffleSpells() {
        Game::get()->deck_spells->moveAllCardsInLocation(CardLocation::Discard(), CardLocation::Deck());
        Game::get()->deck_spells->shuffle(CardLocation::Deck());
    }
}
