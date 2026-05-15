<?php

namespace WizardsGrimoireExt\Cards\Forbidden_Scrolls;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;

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