<?php

namespace WizardsGrimoire\Cards\Base_1;

use BgaSystemException;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class ArcaneTactics extends BaseCard {

    public function castSpell($args)
    {
        // Gain 7 mana cards from the mana deck. Then place 4 mana cards from your hand on top of the mana deck in any order
        $this->drawManaCards(7);
    }

    public function castSpellInteraction($args)
    {
        $mana_ids = explode(",", array_shift($args));
        if(sizeof($mana_ids) != 4) {
            throw new BgaSystemException("Need to select 4 mana cards");
        }

        $cards_before = [];
        $cards_after = [];

        foreach ($mana_ids as $mana_id) {
            $cards_before[] = ManaCard::isInHand($mana_id);
            ManaCard::addOnTopOfDeck($mana_id);
            $cards_after[] = ManaCard::get($mana_id);
        }

        Notifications::moveManaCard(Players::getPlayerId(), $cards_before, $cards_after);
    }
}
