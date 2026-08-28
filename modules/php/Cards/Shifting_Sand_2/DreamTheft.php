<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Players;

class DreamTheft extends BaseCard {

    public function castSpell($args) {
        // Gain 3 mana. You opponent must give you the highest powermana in their hand
        $this->drawManaCards(3);

        $opponentId = Players::getOpponentId();
        $opponentHand = ManaCard::getHand($opponentId);
        if (count($opponentHand) == 0) {
            return; 
        }

        $highestPowerMana = 0;
        $highestPowerManaCard = null;
        foreach ($opponentHand as $mana) {
            $power = ManaCard::getPower($mana);
            if ($highestPowerMana === 0 || $power > $highestPowerMana) {
                $highestPowerMana = $power;
                $highestPowerManaCard = $mana;
            }
        }

        if ($highestPowerManaCard !== null) {
            ManaCard::addCardsToHand([$highestPowerManaCard], Players::getPlayerId());
        }

    }

}