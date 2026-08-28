<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class Belch extends BaseCard {

    public function castSpell($args) {
        // Reveal 2 mana cards from the mana deck. Deal damage equal to the highest power mana revealed. Discard them or return them in any order
        $cards = ManaCard::revealFromDeck(2);
        usort($cards, function ($card1, $card2) {
            return ManaCard::getPower($card1) < ManaCard::getPower($card2) ? 1 : -1;
        });
        $highestCard = array_shift($cards);
        $dmg = ManaCard::getPower($highestCard);
        $this->dealDamage($dmg);
    }

    public function castSpellInteraction($args)
    {
        $deck_card_ids = explode(",", array_shift($args));
        $discard_card_ids = explode(",", array_shift($args));

        $cards_before = ManaCard::getRevealedMana();
        foreach( $deck_card_ids as $card_id ) {
            if( $card_id == "" ) continue;
            $card = ManaCard::addOnTopOfDeck(intval($card_id));
        }

        foreach( $discard_card_ids as $card_id ) {
            if( $card_id == "" ) continue;
            $card = ManaCard::addOnTopOfDiscard(intval($card_id));
        }

        Notifications::moveManaCard(Players::getPlayerId(), $cards_before, false);
    }

}