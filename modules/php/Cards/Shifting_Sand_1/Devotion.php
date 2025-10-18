<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;

class Devotion extends BaseCard {

    public function castSpell($args) {
        // Choose 1: Gain 2 manas, or discard a mana card off 1 of your other spells
        if ($args == null || $args == "") {
            $this->drawManaCards(2);
        } else {
            $position = intval(array_shift($args));
            ManaCard::discardManaFromSpell($position);
        }
    }

}