<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Cards\BaseCard;
use BgaSystemException;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class EnergyReserve extends BaseCard {

    public function castSpell($args) {
        // Pick up a mana card off 1 of your others spells. Gain mana equal to that mana's power.
        $position = intval(array_shift($args));
        $card = ManaCard::getOnTopOnManaCoolDown($position);

        if ($card == null) {
            throw new BgaSystemException("No card");
        }

        ManaCard::addToHand($card['id']);
        $card_after = ManaCard::get($card['id']);
        Notifications::moveManaCard(Players::getPlayerId(), [$card], [$card_after]);

        $mana_power = ManaCard::getPower($card);
        $this->drawManaCards($mana_power);
    }
}
