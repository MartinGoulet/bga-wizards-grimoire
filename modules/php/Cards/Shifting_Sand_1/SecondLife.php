<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\Events;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Players;

class SecondLife extends BaseCard {

    public function castSpell($args) {
        // Pick up a mana off 1 of your other spells
        $mana_deck_pos = array_shift($args);
        $player_id = Players::getPlayerId();
        $mana_card = ManaCard::hasUnderSpell($mana_deck_pos, $player_id);

        $position = ManaCard::isOnTopOfSpell($mana_card);
        ManaCard::addCardsToHand([$mana_card]);
        Events::onManaPickedUpUnderSpell($position);
    }

    public function castSpellInteraction($args)
    {
        $fracture = new \WizardsGrimoireExt\Cards\Base_2\Fracture();
        $fracture->castSpellInteraction($args);
    }

}