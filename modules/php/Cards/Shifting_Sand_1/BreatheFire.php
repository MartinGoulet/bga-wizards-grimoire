<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Players;
use WizardsGrimoireExt\Core\SpellCard;

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