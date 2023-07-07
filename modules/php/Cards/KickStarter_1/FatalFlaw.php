<?php

namespace WizardsGrimoire\Cards\KickStarter_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class FatalFlaw extends BaseCard {

    public function castSpell($args) {
        // Deal 1 damage. Reveal the top mana card on 1 of your opponents spells, dealing additional damage equal to it's power.
        $this->dealDamage(1);

        if($args == null || $args == "") {
            $this->dealDamage(0);
        } else {
            $position = intval(array_shift($args));
            $card = ManaCard::getOnTopOnManaCoolDown($position, Players::getOpponentId());
            Notifications::revealManaCardCooldown(Players::getOpponentId(), $card, $this->getCardName());
            $this->dealDamage(ManaCard::getPower($card));
        }
    }
}
