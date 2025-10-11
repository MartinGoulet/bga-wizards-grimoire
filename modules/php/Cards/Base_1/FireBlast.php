<?php

namespace WizardsGrimoireExt\Cards\Base_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\Players;

class FireBlast extends BaseCard {

    public function castSpell($args) {
        // Discard all mana in your hand. Deal 7 damage
        $player_id = Players::getPlayerId();
        $cards = ManaCard::getHand();

        foreach ($cards as $card_id => $card) {
            ManaCard::addOnTopOfDiscard($card_id);
        }
        Notifications::discardManaCards($player_id, $cards);

        $this->dealDamage(7);
    }
}
