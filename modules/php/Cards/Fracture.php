<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\Assert;
use WizardsGrimoire\Core\Game;
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
            $deck_mana = Game::get()->deck_manas;

            $src_top_card = Assert::hasManaCardUnderSpell($src_deck_pos);

            ManaCard::addOnTopOfManaCoolDown($src_top_card['id'], $dest_deck_pos);
            $dest_top_card = $deck_mana->getCard($src_top_card['id']);

            Notifications::moveManaCard($player_id, [$src_top_card], [$dest_top_card]);
        }
    }
}
