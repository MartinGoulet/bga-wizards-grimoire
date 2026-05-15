<?php

namespace WizardsGrimoireExt\Cards\Base_2;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\Events;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\Players;

class SilentSupport extends BaseCard {

    public function castSpell($args) {
        // Each time you discard off this spell, pick up a mana off 1 of your other spells

        $position = intval(array_shift($args));
        if($position == 0) {
            Notifications::spellNoEffect();
            return;
        }

        $card = ManaCard::hasUnderSpell($position);
        ManaCard::addToHand($card['id']);
        Notifications::pickUpManaCardFromSpell(Players::getPlayerId(), $card, $position);
        Notifications::moveManaCard(Players::getPlayerId(), [$card]);
        Events::onManaPickedUpUnderSpell($position);
    }


}
