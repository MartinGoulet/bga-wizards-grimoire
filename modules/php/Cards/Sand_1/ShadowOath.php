<?php

namespace WizardsGrimoireExt\Cards\Sand_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\Events;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Players;

class ShadowOath extends BaseCard {

    public function castSpell($args) {
        // Pick up a mana card off one of your other spells. You opponent gains 1 mana card

        // Deal damage and gain mana equal to that mana's power
        $mana_deck_pos = array_shift($args);
        $player_id = Players::getPlayerId();
        $mana_card = ManaCard::hasUnderSpell($mana_deck_pos, $player_id);

        $position = ManaCard::isOnTopOfSpell($mana_card);
        ManaCard::addCardsToHand([$mana_card]);
        Events::onManaPickedUpUnderSpell($position);

        $this->drawManaCards(1, Players::getOpponentId());
    }
}
