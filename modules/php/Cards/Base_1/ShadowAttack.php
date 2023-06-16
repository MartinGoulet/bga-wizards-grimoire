<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class ShadowAttack extends BaseCard {

    public function castSpell($args) {
        // Discard a mana card off 1 of your other spells. 
        // Deal damage and gain mana equal to that mana's power
        $mana_deck_pos = array_shift($args);
        $player_id = Players::getPlayerId();

        $card = ManaCard::hasUnderSpell($mana_deck_pos, $player_id);

        ManaCard::addOnTopOfDiscard($card['id']);

        $cardAfter = ManaCard::get($card['id']);
        Notifications::moveManaCard($player_id, [$card], [$cardAfter]);

        $power = intval($card['type']);

        $this->dealDamage($power);
        $this->drawManaCards($power);
    }
}
