<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Events;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class ToxicGift extends BaseCard {

    public function castSpell($args) {
        // Give your opponent a mana card from your hand. If you do, deal damage equal to its power
        if (sizeof($args) !== 1) {
            Notifications::spellNoEffect();
            return;
        }
        $card_id = intval(array_shift($args));
        $card = ManaCard::isInHand($card_id);

        ManaCard::addToHand($card["id"], Players::getOpponentId());
        Notifications::moveManaCard(Players::getPlayerId(), [$card], false);
        Events::onAddCardToHand();

        $damage = ManaCard::getPower($card);
        $this->dealDamage($damage);
    }
}
