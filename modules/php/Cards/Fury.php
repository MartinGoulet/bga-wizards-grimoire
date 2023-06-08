<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class Fury extends BaseCard {

    public function castSpell($args) {
        // Deal 1 damage for each of your opponent's spells that have mana on them
        $opponent_id = Players::getOpponentId();

        $total = 0;
        for ($i = 1; $i <= 6; $i++) {
            $count = Game::get()->deck_manas->countCardInLocation(CardLocation::PlayerManaCoolDown($opponent_id, $i));
            if ($count > 0) {
                $total++;
            }
        }

        $this->dealDamage($total);
    }
}
