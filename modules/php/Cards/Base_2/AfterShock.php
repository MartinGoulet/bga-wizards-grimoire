<?php

namespace WizardsGrimoire\Cards\Base_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class AfterShock extends BaseCard {

    public function castSpell($args) {
        // Gain 1 mana card. Place 1 mana card on the mana deck and deal damage equal to its power
        $this->drawManaCards(1);
    }

    public function castSpellInteraction($args) {
        $card_id = intval(array_shift($args));
        $card = ManaCard::isInHand($card_id);
        ManaCard::addOnTopOfDeck($card_id);
        $card_after = ManaCard::Get($card_id);
        Notifications::moveManaCard(Players::getPlayerId(), [$card], [$card_after]);
        $this->dealDamage(ManaCard::getPower($card));
    }
}
