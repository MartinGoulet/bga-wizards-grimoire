<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_1;

use WizardsGrimoire\Cards\BaseCard;

class Unchained extends BaseCard {

    public function castSpell($args) {
        $options = intval(array_shift($args));

        if(!in_array($options, [1, 2])) {
            throw new \BgaSystemException('Invalid option for Unchained spell: ' . $options);
        }

        if ($options == 2) {
            // Gain 5 health
            $this->healPlayer(5);
        } else {
            // Deal 5 damage
            $this->dealDamage(5);
        }
    }

}