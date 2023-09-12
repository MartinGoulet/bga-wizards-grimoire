<?php

namespace WizardsGrimoire\Cards\Base_1;

use BgaSystemException;
use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class ArcaneTactics extends BaseCard {

    public function castSpell($args) {
        // Gain 7 mana cards from the mana deck. Then place 4 mana cards from your hand on top of the mana deck in any order
        ManaCard::draw(7);
    }

    public function castSpellInteraction($args) {
        $mana_ids = explode(",", array_shift($args));
        if (sizeof($mana_ids) != 4) {
            throw new BgaSystemException("Need to select 4 mana cards");
        }

        $cards = [];

        foreach ($mana_ids as $mana_id) {
            $cards[] = ManaCard::isInHand($mana_id);
            ManaCard::addOnTopOfDeck($mana_id);
        }

        Notifications::moveManaCard(Players::getPlayerId(), $cards);

        // $remaining_cards = ManaCard::getRevealedMana();
        // ManaCard::addCardsToHand($remaining_cards);
        // Notifications::moveManaCard(Players::getPlayerId(), $remaining_cards);
    }
}
