<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Events;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class SharedPower extends BaseCard {

    public function castSpell($args) {

        // You may give your opponent 1 mana card from your hand. 
        // If you do, gain 4 mana cards
        if (sizeof($args) == 0) {
            Notifications::spellNoEffect();
            return;
        }

        $mana_id = array_shift($args);
        $opponent_id = Players::getOpponentId();

        $card = ManaCard::isInHand($mana_id);

        // Give opponent 1 mana

        ManaCard::addToHand($mana_id, $opponent_id);
        Notifications::moveManaCard($opponent_id, [$card]);
        Events::onAddCardToHand();

        // Gain 4 mana cards
        $this->drawManaCards(4);
    }
}
