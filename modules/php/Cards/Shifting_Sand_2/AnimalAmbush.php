<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class AnimalAmbush extends BaseCard {

    public function castSpell($args) {

        $this->dealDamage(2);

        if(!empty($args)) {
            $opponent_deck_position = intval(array_shift($args));
            ManaCard::dealFromDeckToManaCoolDown($opponent_deck_position, Players::getOpponentId());
        } else {
            Notifications::spellNoEffect();
        }
    }

}