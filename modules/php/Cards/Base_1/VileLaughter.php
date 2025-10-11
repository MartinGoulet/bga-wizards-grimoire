<?php

namespace WizardsGrimoireExt\Cards\Base_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\Game;
use WizardsGrimoireExt\Core\ManaCard;

class VileLaughter extends BaseCard {

    public function castSpell($args) {
        // Deal 6 damage, minus the highest power mana in your hand
        $player_id = Game::get()->getActivePlayerId();
        $cards = ManaCard::getHand();

        $max_value = ManaCard::getMaxValue($cards);
        $this->dealDamage(6 - $max_value);
    }
}
