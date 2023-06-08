<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class FireBlast extends BaseCard {

    public function castSpell($args) {
        // Discard all mana in your hand. Deal 7 damage
        $player_id = Players::getPlayerId();
        $cards = Players::getHand();

        $cards_after = [];
        foreach ($cards as $card_id => $card) {
            $cards_after[] = Game::get()->deck_manas->insertCardOnExtremePosition($card_id, CardLocation::Discard(), true);
        }
        Notifications::moveManaCard($player_id, $cards, $cards_after);

        $this->dealDamage(7);
    }
}
