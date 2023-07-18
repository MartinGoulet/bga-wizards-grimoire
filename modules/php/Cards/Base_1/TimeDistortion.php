<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Events;
use WizardsGrimoire\Core\ManaCard;

class TimeDistortion extends BaseCard {

    public function castSpell($args) {
        // Pick up a mana card of 2 of your other spells

        $mana_cards_id = explode(",",  array_shift($args));
        $mana_cards = ManaCard::getCards($mana_cards_id);

        // Verify if card is on top of the spell deck (first card in mana cooldown deck)
        // Note : Since mana card is not moved, the current spell card played didn't have
        //        any mana card under it.
        foreach ($mana_cards as $card_id => $card) {
            $position = ManaCard::isOnTopOfSpell($card);
            Events::onManaPickedUpUnderSpell($position);
        }

        ManaCard::addCardsToHand($mana_cards);
    }
}
