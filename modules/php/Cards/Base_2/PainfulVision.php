<?php

namespace WizardsGrimoire\Cards\Base_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\SpellCard;

class PainfulVision extends BaseCard {

    public function castSpell($args) {
        // Deal 5 damage. Place a mana from the mana deck on all your spells with 1 mana card on them
        $this->dealDamage(5);

        $spells = SpellCard::getCardsFromRepertoire();
        foreach ($spells as $spell_id => $spell) {
            $position = SpellCard::getPositionInRepertoire($spell);
            $count = ManaCard::countOnTopOfManaCoolDown($position);
            if ($count == 1) {
                ManaCard::dealFromDeckToManaCoolDown($position);
            }
        }
    }
}
