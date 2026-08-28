<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\SpellCard;

class Trample extends BaseCard {

    public function castSpell($args) {
        // Deal 1 damage. Deal an additional 2 damage for each other spell you cast this turn that cost 3 or greater
        $spellsPlayed = Globals::getPlayedSpellsThisTurn();
        $additionalDamage = 0;
        foreach ($spellsPlayed as $spellId) {
            $spell = SpellCard::get($spellId);
            $card_type = SpellCard::getCardInfo($spell);
            if ($card_type['class'] !== 'Trample' && $card_type['cost'] >= 3) {
                $additionalDamage += 2;
            }
        }

        $this->dealDamage(1 + $additionalDamage);
    }

}