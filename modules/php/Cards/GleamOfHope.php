<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class GleamOfHope extends BaseCard {

    public function castSpell($args) {
        
        // Gain mana until you have 5 mana cards in your hand
        $player_id = Players::getPlayerId();
        $hand_count = Game::get()->$deck_manas->countCardInLocation(CardLocation::Hand(), $player_id);

        $this->drawManaCards(5 - $hand_count);
    }

}
