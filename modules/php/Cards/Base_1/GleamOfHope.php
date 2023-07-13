<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;

class GleamOfHope extends BaseCard {

    public function castSpell($args) {

        // Gain mana until you have 5 mana cards in your hand
        $hand_count = ManaCard::getHandCount();

        if ($hand_count < 5) {
            $this->drawManaCards(5 - $hand_count);
        } else {
            Notifications::spellNoEffect();
        }
    }
}
