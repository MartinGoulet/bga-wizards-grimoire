<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;

class SleightOfHand extends BaseCard {

    public function castSpell($args) {
        // Gain 1 mana card. Gain additional mana equal to that mana's power
        $card = array_shift($this->drawManaCards(1));

        $mana_power = ManaCard::getPower($card);
        $this->drawManaCards($mana_power);
    }
}
