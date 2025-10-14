<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;

class SplitSoul extends BaseCard {

    public function castSpell($args) {
        // Choose 1: Gain 4 mana, or discard a mana card off 2 of your other spells
        if ($args == null || $args == "") {
            $this->drawManaCards(4);
        } else {
            $values = explode(',', array_shift($args));

            if (sizeof($values) > 0) {
                $position = intval(array_shift($values));
                ManaCard::discardManaFromSpell($position);
            }
            if (sizeof($values) > 0) {
                $position = intval(array_shift($values));
                ManaCard::discardManaFromSpell($position);
            }
        }
    }

}