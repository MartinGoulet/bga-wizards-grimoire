<?php

namespace WizardsGrimoire\Cards\Base_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Events;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class Fracture extends BaseCard {

    public function castSpell($args) {

        // Gain 4 mana cards. 
        $this->drawManaCards(4);

        // You may move a mana card between 2 of your other spells
        if (sizeof($args) == 2) {
            $src_deck_pos = intval(array_shift($args));
            $dest_deck_pos = intval(array_shift($args));
            $player_id = Players::getPlayerId();

            $src_top_card = ManaCard::hasUnderSpell($src_deck_pos);

            ManaCard::addOnTopOfManaCoolDown($src_top_card['id'], $dest_deck_pos);

            Notifications::moveManaCard($player_id, [$src_top_card]);
            Events::onAddManaUnderSpell($player_id, $dest_deck_pos);
            Events::onManaDiscarded($src_top_card, $src_deck_pos, $player_id);
        }
    }
}
