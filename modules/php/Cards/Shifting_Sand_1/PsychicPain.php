<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class PsychicPain extends BaseCard {

    public function castSpell($args) {

        // Place 1 mana card from your hand on top of the mana deck. 
        // If you do, deal 2 damages
        if (sizeof($args) == 0) {
            Notifications::spellNoEffect();
            return;
        }

        $mana_id = array_shift($args);
        ManaCard::isInHand($mana_id);

        ManaCard::addOnTopOfDeck($mana_id);
        $topCard = ManaCard::get($mana_id);
        Notifications::moveManaCard(Players::getPlayerId(), [$topCard]);

        $this->dealDamage(2);

    }

}