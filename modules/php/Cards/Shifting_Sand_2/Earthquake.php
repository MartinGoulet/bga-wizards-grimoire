<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Players;

class Earthquake extends BaseCard {

    public function castSpell($args) {
        // Deal 2 damage for each card with unique mana power in your opponent's hand
        $manas = ManaCard::getHand(Players::getOpponentId());
        $uniquePowers = [];
        foreach ($manas as $mana) {
            $power = ManaCard::getPower($mana);
            if (!in_array($power, $uniquePowers)) {
                $uniquePowers[] = $power;
            }
        }
        $this->dealDamage(2 * count($uniquePowers));
    }

}