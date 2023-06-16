<?php

namespace WizardsGrimoire\Cards;

class SleightOfHand extends BaseCard {

    public function castSpell($args) {
        // Gain 1 mana card. Gain additional mana equal to that mana's power
        $cards = $this->drawManaCards(1);

        $mana_power = intval(array_shift($cards)['type']);
        $this->drawManaCards($mana_power);
    }
}
