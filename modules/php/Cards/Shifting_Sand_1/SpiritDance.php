<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Events;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class SpiritDance extends BaseCard {

    public function castSpell($args) {

        // You may move a mana card between 2 of your other spells
        if (sizeof($args) != 2) {
            $this->drawManaCards(6);
            return;
        }

        $this->drawManaCards(4);

        $src_deck_pos = intval(array_shift($args));
        $dest_deck_pos = intval(array_shift($args));
        $player_id = Players::getOpponentId();

        $src_top_card = ManaCard::hasUnderSpell($src_deck_pos, $player_id);

        ManaCard::addOnTopOfManaCoolDown($src_top_card['id'], $dest_deck_pos, $player_id);

        Notifications::moveOpponentManaCard($player_id, $src_top_card, $src_deck_pos, $dest_deck_pos);
        Events::onAddManaUnderSpell($player_id, $dest_deck_pos);
        Events::onManaPickedUpUnderSpell($src_deck_pos, $player_id);
    }

}