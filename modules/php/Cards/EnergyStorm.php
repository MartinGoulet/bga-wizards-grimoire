<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class EnergyStorm extends BaseCard {

    public function castSpell($args) {
        // Deal 3 damage. Deal 1 additional damage for each 4 power mana in your hand
        $player_id = Players::getPlayerId();
        $mana_cards = ManaCard::getHand();

        $nbr_4_powers = sizeof(array_filter($mana_cards, function ($card) {
            return $card['type'] == 4;
        }));

        $this->dealDamage(3 + $nbr_4_powers);
    }
}
