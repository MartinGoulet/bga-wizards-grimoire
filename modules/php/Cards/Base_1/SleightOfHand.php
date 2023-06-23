<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;

class SleightOfHand extends BaseCard {

    public function castSpell($args) {
        // Gain 1 mana card. Gain additional mana equal to that mana's power
        $cards = $this->drawManaCards(1);
        $card = array_shift($cards);

        $mana_power = ManaCard::getPower($card);
        $this->drawManaCards($mana_power);
    }
}
