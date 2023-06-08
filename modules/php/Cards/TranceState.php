<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class TranceState extends BaseCard {

    public function castSpell($args) {
        // Deal 3 damage, minus 1 damage for each mana card in your hand
        $player_id = Players::getPlayerId();
        $hand_count = Game::get()->deck_manas->countCardInLocation(CardLocation::Hand(), $player_id);

        $damage = max([3 - $hand_count, 0]);
        $this->dealDamage($damage);
    }
}
