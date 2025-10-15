<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Players;

class SoulShatter extends BaseCard {

    public function castSpell($args) {
        $mana_cards = ManaCard::getHand(Players::getOpponentId());
        $max_mana_power = array_reduce($mana_cards, function ($carry, $item) {
            return max($carry, ManaCard::getPower($item));
        }, 0);
        $this->dealDamage(4 + $max_mana_power);
    }

}