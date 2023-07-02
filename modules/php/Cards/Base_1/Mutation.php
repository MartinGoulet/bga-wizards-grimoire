<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;

class Mutation extends BaseCard {

    public function castSpell($args) {
        // Gain 1 mana card. Deal damage equal to that mana's power
        $cards = $this->drawManaCards(1);
        $card = array_shift($cards);

        $mana_power = ManaCard::getPower($card);
        $this->dealDamage($mana_power);
    }
}
