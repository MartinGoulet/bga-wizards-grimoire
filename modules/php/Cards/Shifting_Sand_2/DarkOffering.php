<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Players;

class DarkOffering extends BaseCard {

    public function castSpell($args) {
        // Pick up a mana card off 1 of your other spells and give it to your opponent. Deal damage equals to its power
        $position = intval(array_shift($args));

        $manaCard = ManaCard::getOnTopOnManaCoolDown($position);

        $power = ManaCard::getPower($manaCard);
        $this->dealDamage($power);

        ManaCard::addCardsToHand([$manaCard], Players::getOpponentId());
    }

}