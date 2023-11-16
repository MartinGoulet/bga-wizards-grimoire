<?php

namespace WizardsGrimoire\Cards\Base_1;

use BgaSystemException;
use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Events;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class EnergyReserve extends BaseCard {

    public function castSpell($args) {
        // Pick up a mana card off 1 of your other spells. Gain mana equal to that mana's power.
        $position = intval(array_shift($args));
        $card = ManaCard::getOnTopOnManaCoolDown($position);

        if ($card == null) {
            throw new BgaSystemException("No card");
        }

        ManaCard::addToHand($card['id']);
        Notifications::pickUpManaCardFromSpell(Players::getPlayerId(), $card, $position);
        Notifications::moveManaCard(Players::getPlayerId(), [$card]);
        Events::onManaPickedUpUnderSpell($position);

        $mana_power = ManaCard::getPower($card);
        $this->drawManaCards($mana_power);
    }
}
