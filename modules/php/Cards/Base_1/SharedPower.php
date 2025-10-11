<?php

namespace WizardsGrimoireExt\Cards\Base_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\Players;

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
        Notifications::giveManaCards(Players::getPlayerId(), [$card]);

        // Gain 4 mana cards
        $this->drawManaCards(4);
    }
}
