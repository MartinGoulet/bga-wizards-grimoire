<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class FireBlast extends BaseCard {

    public function castSpell($args) {
        // Discard all mana in your hand. Deal 7 damage
        $player_id = Players::getPlayerId();
        $cards = ManaCard::getHand();

        foreach ($cards as $card_id => $card) {
            ManaCard::addOnTopOfDiscard($card_id);
        }
        Notifications::moveManaCard($player_id, $cards, false);

        $this->dealDamage(7);
    }
}
