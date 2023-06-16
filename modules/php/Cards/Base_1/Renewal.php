<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Core\ManaCard;

class Renewal extends BaseCard {

    public function castSpell($args) {
        // Gain mana until you have 4 mana cards in your hand
        $hand_count = ManaCard::getHandCount();

        $nbr_cards = max([4 - $hand_count, 0]);
        $this->drawManaCards($nbr_cards);
    }
}
