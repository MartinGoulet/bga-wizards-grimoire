<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\Players;

class GleamOfHope extends BaseCard {

    public function castSpell($args) {

        // Gain mana until you have 5 mana cards in your hand
        $hand_count = Players::getHandCount();

        $this->drawManaCards(5 - $hand_count);
    }
}
