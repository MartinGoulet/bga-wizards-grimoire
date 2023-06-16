<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Core\ManaCard;

class GleamOfHope extends BaseCard {

    public function castSpell($args) {

        // Gain mana until you have 5 mana cards in your hand
        $hand_count = ManaCard::getHandCount();

        $this->drawManaCards(5 - $hand_count);
    }
}
