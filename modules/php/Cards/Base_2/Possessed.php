<?php

namespace WizardsGrimoire\Cards\Base_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Events;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class Possessed extends BaseCard {

    public function castSpell($args) {
        // Deal 5 damage. Your opponent may give you a mana from their hand. If they do, reduce your damage by the mana's power
    }

    public function castSpellInteraction($args) {
        if ($args == null || $args == "") {
            $this->dealDamage(5);
        } else {
            $card_id = intval(array_shift($args));

            $card = ManaCard::isInHand($card_id, Players::getOpponentId());
            ManaCard::addToHand($card_id);
            Notifications::moveManaCard(Players::getPlayerId(), [$card], false);
            Events::onAddCardToHand();

            $this->dealDamage(5 - ManaCard::getPower($card));
        }
    }
}
