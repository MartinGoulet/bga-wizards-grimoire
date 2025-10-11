<?php

namespace WizardsGrimoireExt\Cards\Base_2;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\Events;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\Players;

class Fracture extends BaseCard {

    public function castSpell($args) {
        // Gain 4 mana cards. 
        $this->drawManaCards(4);
    }

    public function castSpellInteraction($args) {
        // You may move a mana card between 2 of your other spells
        if (sizeof($args) == 2) {
            $src_deck_pos = intval(array_shift($args));
            $dest_deck_pos = intval(array_shift($args));
            $player_id = Players::getPlayerId();

            $src_top_card = ManaCard::hasUnderSpell($src_deck_pos);

            ManaCard::addOnTopOfManaCoolDown($src_top_card['id'], $dest_deck_pos);

            Notifications::fracture($player_id, $src_top_card, $src_deck_pos, $dest_deck_pos);
            Events::onAddManaUnderSpell($player_id, $dest_deck_pos);
            Events::onManaPickedUpUnderSpell($src_deck_pos, $player_id);
        }
    }
}
