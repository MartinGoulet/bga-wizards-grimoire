<?php

namespace WizardsGrimoire\Cards\Base_1;

class Mutation extends BaseCard {

    public function castSpell($args) {
        // Gain 1 mana card. Deal damage equal to that mana's power
        $cards = $this->drawManaCards(1);

        $mana_power = intval(array_shift($cards)['type']);
        $this->dealDamage($mana_power);
    }
}
