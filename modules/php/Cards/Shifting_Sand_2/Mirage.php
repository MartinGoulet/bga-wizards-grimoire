<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;

class Mirage extends BaseCard {

    public function castSpell($args) {
        // Pick up a mana off 1 of your other spells. If it is a 3 or higher power mana, deal 2 damage
        $position = intval(array_shift($args));
        $mana = ManaCard::getOnTopOnManaCoolDown($position);
        if(ManaCard::getPower($mana) >= 3) {
            $this->dealDamage(2);
        }
        ManaCard::addCardsToHand([$mana]);
    }

}