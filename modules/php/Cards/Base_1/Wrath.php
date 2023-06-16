<?php

namespace WizardsGrimoire\Cards;

use BgaSystemException;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class Wrath extends BaseCard {

    public function castSpell($args) {
        // Your opponent may discard 2 mana cards from their hand. If they do not, deal 2 damage.
        if(ManaCard::getHandCount(Players::getOpponentId()) < 2) {
            Globals::setSkipInteraction(true);
            $this->castSpellInteraction(null);
        }
    }

    public function castSpellInteraction($args) {
        if ($args != null && $args != "") {
            $mana_ids = explode(",", array_shift($args));
            if(sizeof($mana_ids) !== 2) {
                throw new BgaSystemException("You must select exactly 2 mana cards");
            }
            $opponent_id = Players::getOpponentId();
            $cards_before = array_map(function ($mana_id) use ($opponent_id) {
                return ManaCard::isInHand($mana_id, $opponent_id);
            }, $mana_ids);

            foreach ($mana_ids as $card_id) {
                ManaCard::addOnTopOfDiscard($card_id);
            }
            $cards_after = ManaCard::getCards($mana_ids);

            Notifications::moveManaCard(Players::getPlayerId(), $cards_before, $cards_after, "@@@", false);
            $this->dealDamage(0);
        } else {
            $this->dealDamage(2);
        }

    }
}
