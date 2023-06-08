<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Objects\CardLocation;

class FireBlast extends BaseCard {

    public function castSpell($args) {
        // Discard all mana in your hand. Deal 7 damage
        $player_id = Game::getPlayerId();
        $cards = Game::get()->deck_manas->getCardsInLocation(CardLocation::Hand(), $player_id);
        
        $cards_after = [];
        foreach ($cards as $card_id => $card) {
            $cards_after[] = Game::get()->deck_manas->insertCardOnExtremePosition($card_id, CardLocation::Discard(), true);
        }
        Notifications::moveManaCard($player_id, $cards, $cards_before);

        $this->dealDamage(7);
    }

}
