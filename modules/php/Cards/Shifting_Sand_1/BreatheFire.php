<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;

class BreatheFire extends BaseCard {

    public function castSpell($args) {
        $spells = SpellCard::getCardsFromRepertoire(Players::getFirstPlayer());
        $countNbrManasTwo = 0;
        foreach ($spells as $spell) {
            $position = SpellCard::getPositionInRepertoire($spell);
            $count = ManaCard::countOnTopOfManaCoolDown($position);
            if ($count == 2) {
                $countNbrManasTwo++;
            }
        }

        $this->dealDamage($countNbrManasTwo);
    }

}