<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;
use WizardsGrimoire\Objects\CardLocation;

class LivingWind extends BaseCard {

    public function castSpell($args) {
        // Gain 6 mana cards, 
        // gain 1 fewer mana for each of your other spells that have a mana on them
        $player_id = Players::getPlayerId();
        $spells = SpellCard::getCardsFromRepertoire();

        $total = 6;

        foreach ($spells as $card_id => $spell) {
            $card_type = SpellCard::getCardInfo($spell);
            if ($card_type['class'] !== "LivingWind") {
                $pos = intval($spell['location_arg']);
                $count = ManaCard::countOnTopOfManaCoolDown($pos);

                if ($count > 0) {
                    $total--;
                }
            }
        }

        $this->drawManaCards($total);
    }
}
