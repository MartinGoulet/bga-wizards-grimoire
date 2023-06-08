<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class DrainSoul extends BaseCard {

    public function castSpell($args) {
        // Your opponent must give you the highest power card mana in their hand. Deal damage equal to that mana's power
        $opponent_hand = Players::getHand(Players::getOpponentId());

        if (sizeof($opponent_hand) == 0) {
            return;
        }

        $max_value = Game::getMaxManaCardValue($opponent_hand);
        $cards = array_filter($opponent_hand, function ($card) use ($max_value) {
            return intval($card['type']) == $max_value;
        });

        $first_card = array_shift($cards);
        Game::get()->deck_manas->moveCard($first_card['id'], CardLocation::Hand(), Players::getPlayerId());
        $card_after = Game::get()->deck_manas->getCard($first_card['id']);
        Notifications::moveManaCard(Players::getPlayerId(), [$first_card], [$card_after]);

        $this->dealDamage($max_value);
    }
}
