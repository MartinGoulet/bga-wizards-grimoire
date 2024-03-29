<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Events;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class TimeDistortion extends BaseCard {

    public function castSpell($args) {
        // Pick up a mana card off 2 of your other spells

        $mana_cards_id = explode(",",  array_shift($args));
        $mana_cards = ManaCard::getCards($mana_cards_id);

        // Verify if card is on top of the spell deck (first card in mana cooldown deck)
        // Note : Since mana card is not moved, the current spell card played didn't have
        //        any mana card under it.
        $positions = [];
        foreach ($mana_cards as $card_id => $card) {
            $position = ManaCard::isOnTopOfSpell($card);
            Notifications::pickUpManaCardFromSpell(Players::getPlayerId(), $card, $position);
            $positions[] = $position;
        }

        ManaCard::addCardsToHand($mana_cards);

        foreach ($positions as $position) {
            Events::onManaPickedUpUnderSpell($position);
        }
    }
}
