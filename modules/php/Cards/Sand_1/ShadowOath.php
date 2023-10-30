<?php

namespace WizardsGrimoire\Cards\Sand_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Events;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Players;

class ShadowOath extends BaseCard {

    public function castSpell($args) {
        // Pick up a mana card off one of your other spells. You opponent gains 1 mana card
        $mana_card_id = intval(array_shift($args));
        $mana_card = ManaCard::get($mana_card_id);

        $position = ManaCard::isOnTopOfSpell($mana_card);
        ManaCard::addCardsToHand([$mana_card]);
        Events::onManaPickedUpUnderSpell($position);

        $this->drawManaCards(1, Players::getOpponentId());
    }
}
