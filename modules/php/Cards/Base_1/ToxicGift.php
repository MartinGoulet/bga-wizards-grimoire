<?php

namespace WizardsGrimoireExt\Cards\Base_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\Players;

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
        Notifications::giveManaCards(Players::getPlayerId(), [$card]);

        $damage = ManaCard::getPower($card);
        $this->dealDamage($damage);
    }
}
