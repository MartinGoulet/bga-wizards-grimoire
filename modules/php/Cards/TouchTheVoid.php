<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class TouchTheVoid extends BaseCard {

    public function castSpell($args) {
        // Deal damage equal to the quantity of mana cards on 1 of your spells
        $player_id = Players::getPlayerId();
        $max_value = 0;

        for ($i = 1; $i <= 6; $i++) {
            $count = Game::get()->deck_manas->countCardInLocation(CardLocation::PlayerManaCoolDown($player_id, $i));
            if ($count > $max_value) {
                $max_value = $count;
            }
        }

        $this->dealDamage($max_value);
    }
}
