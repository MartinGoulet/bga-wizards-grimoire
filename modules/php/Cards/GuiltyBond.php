<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\Assert;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class GuiltyBond extends BaseCard {

    public function castSpell($args)
    {
        // Show your opponent a mana from your hand. Deal 2 damage if they have a mana of the same power in their hand
        $mana_id = intval(array_shift($args));
        $card = Assert::isCardInHand($mana_id);
        $mana_power = intval($card['type']);

        Notifications::revealManaCard(Players::getPlayerId(), $mana_power);

        $opponent_hand = Players::getHand(Players::getOpponentId());
        $cards_same_power = array_filter($opponent_hand, function($card) use($mana_power) {
            return intval($card['type']) == $mana_power;
        });

        if(sizeof($cards_same_power) > 0) {
            Notifications::revealManaCard(Players::getOpponentId(), $mana_power);
            $this->dealDamage(2);
        } else {
            Notifications::hasNoManaCard(Players::getOpponentId(), $mana_power);
            Notifications::spellNoEffect();
        }
    }

}
