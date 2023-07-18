<?php

namespace WizardsGrimoire\Cards\Base_1;

use BgaSystemException;
use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Events;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class Wrath extends BaseCard {

    public function castSpell($args) {
        // Your opponent may discard 2 mana cards from their hand. If they do not, deal 2 damage.
        if (ManaCard::getHandCount(Players::getOpponentId()) < 2) {
            Globals::setSkipInteraction(true);
            $this->dealDamage(2);
        }
    }

    public function castSpellInteraction($args) {
        if ($args != null && $args != "") {
            $mana_ids = explode(",", array_shift($args));
            if (sizeof($mana_ids) !== 2) {
                throw new BgaSystemException("You must select exactly 2 mana cards");
            }
            $opponent_id = Players::getOpponentId();
            $cards_before = array_map(function ($mana_id) use ($opponent_id) {
                return ManaCard::isInHand($mana_id, $opponent_id);
            }, $mana_ids);

            foreach ($mana_ids as $card_id) {
                ManaCard::addOnTopOfDiscard($card_id);
            }

            Notifications::moveManaCard(Players::getPlayerId(), $cards_before, false);
            Events::onHandCountChanged();

            $this->dealDamage(0);
        } else {
            $this->dealDamage(2);
        }
    }
}
