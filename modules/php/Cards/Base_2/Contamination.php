<?php

namespace WizardsGrimoireExt\Cards\Base_2;

use BgaSystemException;
use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\Players;

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

        foreach ($cards as $_ => $card) {
            ManaCard::addOnTopOfDeck($card['id']);
        }

        Notifications::moveManaCard(Players::getPlayerId(), $cards);
        $this->dealDamage(4);
    }
}
