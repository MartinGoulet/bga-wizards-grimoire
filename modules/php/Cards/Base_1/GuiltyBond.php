<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class GuiltyBond extends BaseCard {

    public function castSpell($args) {
        // Show your opponent a mana from your hand. Deal 2 damage if they have a mana of the same power in their hand
        $mana_id = intval(array_shift($args));
        $card = ManaCard::isInHand($mana_id);

        Notifications::revealManaCardHand(Players::getPlayerId(), [$card]);

        $mana_power = ManaCard::getPower($card);
        $opponent_hand = ManaCard::getHand(Players::getOpponentId());
        $cards_same_power = array_filter($opponent_hand, function ($c) use ($mana_power) {
            return ManaCard::getPower($c) == $mana_power;
        });

        if (sizeof($cards_same_power) > 0) {
            Notifications::revealManaCardHand(Players::getOpponentId(), [array_shift($cards_same_power)]);
            $this->dealDamage(2);
        } else {
            Notifications::hasNoManaCard(Players::getOpponentId(), $mana_power);
            Notifications::spellNoEffect();
        }
    }
}
