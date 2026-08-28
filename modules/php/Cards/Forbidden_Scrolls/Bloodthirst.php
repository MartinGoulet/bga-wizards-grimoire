<?php

namespace WizardsGrimoire\Cards\Forbidden_Scrolls;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;

class Bloodthirst extends BaseCard {

    public function castSpell($args) {

    }

    public function onAfterDiscardManaFromSpell(int $mana_id) {
        $ownerId = $this->getOwnerId();
        $hand = ManaCard::getHand($ownerId);
        $highestPower = 0;
        foreach ($hand as $mana) {
            if (ManaCard::getPower($mana) > $highestPower) {
                $highestPower = ManaCard::getPower($mana);
            }
        }

        $damage = max(3 - $highestPower, 0);
        $this->dealDamage($damage);
    }

}