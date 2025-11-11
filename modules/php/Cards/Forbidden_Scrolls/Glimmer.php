<?php

namespace WizardsGrimoireExt\Cards\Forbidden_Scrolls;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\Players;

class Glimmer extends BaseCard {

    public function castSpell($args) {
        // Gain mana until you gain a mana card that is 2 power of greater. Gain 3 more mana cards
        $this->gainManaUntilPower(2);

        $this->drawManaCards(3);
    }

    private function gainManaUntilPower($power) {
        $gainedManaCards = [];

        while (true) {
            $manaCard = $this->drawManaCards(1);
            if(empty($manaCard)) {
                break;
            }
            
            $manaCard = array_shift($manaCard);
            $gainedManaCards[] = $manaCard;

            if (ManaCard::getPower($manaCard) >= $power) {
                break;
            }
        }

        Notifications::moveManaCard(Players::getPlayerId(), $gainedManaCards);
    }

}