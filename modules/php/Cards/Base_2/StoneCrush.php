<?php

namespace WizardsGrimoireExt\Cards\Base_2;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Players;

class StoneCrush extends BaseCard {

    public function castSpell($args) {
        // Deal 1 damage for each mana card in your opponent's hand
        $opponent_id = Players::getOpponentId();
        $hand_count = ManaCard::getHandCount($opponent_id);
        $this->dealDamage($hand_count);
    }
}
