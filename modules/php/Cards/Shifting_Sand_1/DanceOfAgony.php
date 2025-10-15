<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use BgaSystemException;
use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\Players;

class DanceOfAgony extends BaseCard {

    public function castSpell($args) {
        // Deal 4 damage. Discard or gain mana until you have 4 mana cards in your hand
        $this->dealDamage(4);

        $hand_count = ManaCard::getHandCount();

        if ($hand_count == 4) {
            //Do nothing
        } else if ($hand_count < 4) {
            $this->drawManaCards(4 - $hand_count);
        } else {
            $card_ids = explode(",", array_shift($args));
            if (sizeof($card_ids) != ($hand_count - 4)) {
                $nbr_to_discard = $hand_count - 4;
                throw new BgaSystemException("Must discard " . $nbr_to_discard  . " cards");
            }

            $cards = ManaCard::getCards($card_ids);
            foreach ($cards as $card_id => $card) {
                ManaCard::addOnTopOfDiscard($card['id']);
            }
            Notifications::discardManaCards(Players::getPlayerId(), $cards);
        }
    }

}