<?php

namespace WizardsGrimoireExt\Cards\Base_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\Players;

class Betrayal extends BaseCard {

    public function castSpell($args) {
        // Deal 5 damage. Your opponent gains 3 mana cards.
        $this->dealDamage(5);

        $this->drawManaCards(3, Players::getOpponentId());
    }
}
