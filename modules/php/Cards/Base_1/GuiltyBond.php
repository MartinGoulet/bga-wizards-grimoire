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
        $mana_power = ManaCard::getPower($card);

        Notifications::revealManaCard(Players::getPlayerId(), $mana_power);

        $opponent_hand = ManaCard::getHand(Players::getOpponentId());
        $cards_same_power = array_filter($opponent_hand, function ($card) use ($mana_power) {
            return ManaCard::getPower($card) == $mana_power;
        });

        if (sizeof($cards_same_power) > 0) {
            Notifications::revealManaCard(Players::getOpponentId(), $mana_power);
            $this->dealDamage(2);
        } else {
            Notifications::hasNoManaCard(Players::getOpponentId(), $mana_power);
            Notifications::spellNoEffect();
        }
    }
}
