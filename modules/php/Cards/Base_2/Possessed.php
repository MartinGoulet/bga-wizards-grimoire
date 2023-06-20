<?php

namespace WizardsGrimoire\Cards\Base_2;

use ValueError;
use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
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
            $this->dealDamage(5 - ManaCard::getPower($card));
        }
    }
}
