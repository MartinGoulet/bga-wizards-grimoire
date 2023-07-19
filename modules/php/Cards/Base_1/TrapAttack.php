<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Events;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class TrapAttack extends BaseCard {

    public function castSpell($args) {
        // Pick up a mana card off 1 of your other spells. 
        // Deal damage equal to that mana's power
        $position = intval(array_shift($args));
        $player_id = Players::getPlayerId();

        ManaCard::hasUnderSpell($position, $player_id);
        $card = ManaCard::drawFromManaCoolDown($position, $player_id);
        Notifications::moveManaCard($player_id, [$card]);
        Events::onManaPickedUpUnderSpell($position);

        $power = ManaCard::getPower($card);
        $this->dealDamage($power);
    }
}
