<?php

namespace WizardsGrimoire\Cards;

use BgaSystemException;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class SneakyDeal extends BaseCard {

    public function castSpell($args) {
        // Choose 1: Deal 1 damage, or discard a mana card off 1 of your other spells
        if ($args == null || $args == "") {
            $this->dealDamage(1);
        } else {
            $position = intval(array_shift($args));
            $card = ManaCard::getOnTopOnManaCoolDown($position);
            if ($card == null) {
                throw new BgaSystemException("No card found under that spell");
            }
            ManaCard::addOnTopOfDiscard($card['id']);
            $card_after = ManaCard::get($card['id']);

            Notifications::moveManaCard(Players::getPlayerId(), [$card], [$card_after]);
        }
    }
}
