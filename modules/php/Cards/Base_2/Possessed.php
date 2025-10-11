<?php

namespace WizardsGrimoireExt\Cards\Base_2;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\Globals;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\Players;

class Possessed extends BaseCard {

    public function castSpell($args) {
        // Deal 5 damage. Your opponent may give you a mana from their hand. If they do, reduce your damage by the mana's power
        if (ManaCard::getHandCount(Players::getOpponentId()) == 0) {
            Globals::setSkipInteraction(true);
            $this->dealDamage(5);
        }
    }

    public function castSpellInteraction($args) {
        if ($args == null || $args == "") {
            $this->dealDamage(5);
        } else {
            $card_id = intval(array_shift($args));

            $card = ManaCard::isInHand($card_id, Players::getOpponentId());
            ManaCard::addToHand($card_id);
            Notifications::giveManaCards(Players::getPlayerId(), [$card]);

            $this->dealDamage(5 - ManaCard::getPower($card));
        }
    }
}
