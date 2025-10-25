<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_2;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\Players;
use WizardsGrimoireExt\Core\SpellCard;

class Gloom extends BaseCard {

    public function castSpell($args) {
        // Deal 2 damage. You may take a mana of your choice from the discard pile and put it on 1 of your other spells
        $this->dealDamage(2);

        if (count($args) == 2) {
            $discard_mana_id = intval(array_shift($args));
            $position = intval(array_shift($args));
            $cards = [];
            $cards[] = ManaCard::isInDiscard($discard_mana_id);
            ManaCard::addOnTopOfManaCoolDown($discard_mana_id, $position);
            Notifications::moveManaCard(Players::getPlayerId(), $cards);
        }
    }
}
