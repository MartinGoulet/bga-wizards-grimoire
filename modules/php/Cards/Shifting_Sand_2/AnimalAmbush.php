<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_2;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\Players;

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