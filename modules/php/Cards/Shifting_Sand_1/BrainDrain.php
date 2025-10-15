<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Players;

class BrainDrain extends BaseCard {

    public function castSpell($args) {
        $mana_cards = ManaCard::getHand(Players::getPlayerId());
        $min_mana_power = array_reduce($mana_cards, function ($carry, $item) {
            return min($carry, ManaCard::getPower($item));
        }, PHP_INT_MAX);
        $this->dealDamage($min_mana_power);
    }

}