<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class DrainSoul extends BaseCard {

    public function castSpell($args) {
        // Your opponent must give you the highest power card mana in their hand. Deal damage equal to that mana's power
        $opponent_hand = ManaCard::getHand(Players::getOpponentId());

        if (sizeof($opponent_hand) == 0) {
            return;
        }

        $max_value = ManaCard::getMaxValue($opponent_hand);
        $cards = array_filter($opponent_hand, function ($card) use ($max_value) {
            return ManaCard::getPower($card) == $max_value;
        });

        $first_card = array_shift($cards);
        ManaCard::addToHand($first_card['id']);
        Notifications::moveManaCard(Players::getPlayerId(), [$first_card]);

        $this->dealDamage($max_value);
        Game::undoSavepoint();
    }
}
