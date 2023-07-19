<?php

namespace WizardsGrimoire\Cards\Base_1;

use BgaSystemException;
use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Events;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class MistOfPain extends BaseCard {

    public function castSpell($args) {
        // Your opponent may discard up to 4 mana cards from their hand. 
        // For each mana they do not discard, deal 1 damage
        if (ManaCard::getHandCount(Players::getOpponentId()) == 0) {
            Globals::setSkipInteraction(true);
            $this->dealDamage(4);
        }
    }

    public function castSpellInteraction($args) {
        $count = 0;

        if ($args != null && $args != "") {
            $mana_ids = explode(",", array_shift($args));
            if (sizeof($mana_ids) > 4) {
                throw new BgaSystemException("Too much cards");
            }
            $count = sizeof($mana_ids);
            $opponent_id = Players::getOpponentId();
            $cards_before = array_map(function ($mana_id) use ($opponent_id) {
                return ManaCard::isInHand($mana_id, $opponent_id);
            }, $mana_ids);

            foreach ($mana_ids as $card_id) {
                ManaCard::addOnTopOfDiscard($card_id);
            }

            Notifications::moveManaCard(Players::getPlayerId(), $cards_before, false);
        }

        $this->dealDamage(4 - $count);
    }
}
