<?php

namespace WizardsGrimoire\Cards\Base_2;

use BgaSystemException;
use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class Contamination extends BaseCard {

    public function castSpell($args) {
        // Place 2 mana cards from your hand on the mana deck. If you do, deal 4 damage
        if (sizeof($args) < 1) {
            Notifications::spellNoEffect();
            return;
        }

        $card_ids = explode(",", array_shift($args));
        $cards = [];
        foreach ($card_ids as $card_id) {
            $cards[] = ManaCard::isInHand($card_id);
        }

        if (sizeof($cards) != 2) {
            throw new BgaSystemException("Need 2 cards");
        }

        $cards_after = [];
        foreach ($cards as $_ => $card) {
            ManaCard::addOnTopOfDeck($card['id']);
            $cards_after[] = ManaCard::get($card['id']);
        }

        Notifications::moveManaCard(Players::getPlayerId(), $cards, $cards_after);
        $this->dealDamage(4);
    }
}
