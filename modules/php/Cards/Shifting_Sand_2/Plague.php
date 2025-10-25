<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_2;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\Players;

class Plague extends BaseCard {

    public function castSpell($args) {
        // Deal 2 damage. You may deal 2 damage to yourself. If you do, gain 4 mana cards
        $this->dealDamage(2);

        if(count($args) > 0) {
            $this->dealDamage(2, Players::getPlayerId());
            $this->drawManaCards(4);
        }
    }

}