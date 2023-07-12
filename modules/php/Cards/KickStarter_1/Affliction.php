<?php

namespace WizardsGrimoire\Cards\KickStarter_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Players;

class Affliction extends BaseCard {

    public function castSpell($args) {
        // Gain 4 mana cards. You may deal 1 damage to yourself. If you do, gain 2 extra cards
        $this->drawManaCards(4);
    }

    public function castSpellInteraction($args) {
        $gainExtraCard = boolval(array_shift($args));
        if ($gainExtraCard) {
            $this->dealDamage(1, Players::getPlayerId());
            $this->drawManaCards(2);
        }
    }
}
