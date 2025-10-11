<?php

namespace WizardsGrimoireExt\Cards\Base_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;

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
