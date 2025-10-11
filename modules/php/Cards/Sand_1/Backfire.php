<?php

namespace WizardsGrimoireExt\Cards\Sand_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Players;

class Backfire extends BaseCard {

    public function castSpell($args) {
        // Your opponent gains 1 mana card. They take damage equal to its power
        $cards = ManaCard::revealFromDeck(1);
        $card = array_shift($cards);
        $dmg = ManaCard::getPower($card);
        ManaCard::addCardsToHand([$card], Players::getOpponentId());
        $this->dealDamage($dmg);
    }
}
