<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_2;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\Players;

class DarkOffering extends BaseCard {

    public function castSpell($args) {
        // Pick up a mana card off 1 of your other spells and give it to your opponent. Deal damage equals to its power
        $position = intval(array_shift($args));
        $opponentPosition = intval(array_shift($args));

        $manaCard = ManaCard::getOnTopOnManaCoolDown($position);
        $manaCardId = intval($manaCard['id']);

        $power = ManaCard::getPower($manaCard);
        $this->dealDamage($power);

        ManaCard::addOnTopOfManaCoolDown($manaCardId, $opponentPosition, Players::getOpponentId());
        $manaCard = ManaCard::get($manaCardId);
        Notifications::moveManaCard(Players::getOpponentId(), [$manaCard]);
    }

}