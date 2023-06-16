<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Players;

class Freeze extends BaseCard {

    public function castSpell($args)
    {
        // Choose 1:  Gain 4 mana cards, or place a mana card from the mana deck on one of your opponent's spells
        if($args == null || $args == "") {
            $this->drawManaCards(4);
        } else {
            $position = intval(array_shift($args));
            ManaCard::dealFromDeckToManaCoolDown($position, Players::getOpponentId());
        }
    }
}
