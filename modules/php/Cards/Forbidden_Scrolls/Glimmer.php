<?php

namespace WizardsGrimoire\Cards\Forbidden_Scrolls;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

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