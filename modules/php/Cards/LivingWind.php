<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class LivingWind extends BaseCard {

    public function castSpell($args) {
        // Gain 6 mana cards, 
        // gain 1 fewer mana for each of your other spells that have a mana on them
        $player_id = Players::getPlayerId();
        $spells = Game::get()->deck_spells->getCardsInLocation(CardLocation::PlayerSpellRepertoire($player_id));

        $total = 6;

        foreach ($spells as $card_id => $spell) {
            $pos = intval($spell['location_arg']);
            $count = Game::Get()->deck_manas->getCardsInLocation(CardLocation::PlayerManaCoolDown($player_id, $pos));
            if($count > 0) {
                $total--;
            }
        }

        $this->drawManaCards($total);
    }
}
