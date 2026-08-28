<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;

class FireWalk extends BaseCard {

    public function castSpell($args) {
        $this->dealDamage(4);

        // Place any 1 power mana remaining in your hand on top of your mana cooldown.
        $hand = ManaCard::getHand();
        if (count($hand) > 0) {
            $level_1 = array_filter($hand, fn($mana) => ManaCard::getPower($mana) == 1);
            if (count($level_1) > 0) {
                $spell = SpellCard::get($this->id);
                $position = SpellCard::getPositionInRepertoire($spell);

                foreach ($level_1 as $mana) {
                    ManaCard::addOnTopOfManaCoolDown($mana['id'], $position);
                }

                Notifications::moveManaCard(Players::getPlayerId(), $level_1);
            }   
        }
    }

}