<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Events;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;

class TimeDistortion extends BaseCard {

    public function castSpell($args) {
        // Pick up a mana card off 2 of your other spells

        // Verify if card is on top of the spell deck (first card in mana cooldown deck)
        // Note : Since mana card is not moved, the current spell card played didn't have
        //        any mana card under it.
        $positions = explode(",",  array_shift($args));
        $positions = array_map(fn($id) => intval($id), $positions);
        $mana_cards = [];
        foreach ($positions as $position) {
            $mana_card = ManaCard::getOnTopOnManaCoolDown($position);
            $mana_cards[] = $mana_card;
            Notifications::pickUpManaCardFromSpell(Players::getPlayerId(), $mana_card, $position);
        }

        ManaCard::addCardsToHand($mana_cards);

        foreach ($positions as $position) {
            Events::onManaPickedUpUnderSpell($position);
        }
    }
}
