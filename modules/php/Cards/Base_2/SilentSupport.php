<?php

namespace WizardsGrimoire\Cards\Base_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Events;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

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
        Notifications::moveManaCard(Players::getPlayerId(), [$card]);
        Events::onAddCardToHand();
        Events::onManaPickedUpUnderSpell($position);
    }
}
