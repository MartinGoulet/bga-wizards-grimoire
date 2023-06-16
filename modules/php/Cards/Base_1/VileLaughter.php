<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\ManaCard;

class VileLaughter extends BaseCard {

    public function castSpell($args) {
        // Deal 6 damage, minus the highest power mana in your hand
        $player_id = Game::get()->getActivePlayerId();
        $cards = ManaCard::getHand();

        $max_value = ManaCard::getMaxValue($cards);
        $this->dealDamage(6 - $max_value);
    }
}
