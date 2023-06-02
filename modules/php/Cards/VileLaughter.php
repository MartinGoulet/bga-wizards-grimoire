<?php

namespace WizardsGrimoire\Cards;

use BgaUserException;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Objects\CardLocation;

class VileLaughter extends BaseCard {

    public function castSpell($args) {
        // Deal 6 damage, minus the highest power mana in your hand
        $player_id = Game::get()->getActivePlayerId();
        $cards = Game::get()->deck_manas->getCardsInLocation(CardLocation::Hand(), $player_id, 'card_type');

        if(sizeof($cards) === 0) {
            $max_value = 0;
        } else {
            $max_value = max(array_values(array_map(function ($card) {
                return $card['card_type'];
            }, $cards)));
        }

        $this->dealDamage(6 - $max_value);
    }
}
