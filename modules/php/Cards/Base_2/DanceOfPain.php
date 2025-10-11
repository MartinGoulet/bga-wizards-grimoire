<?php

namespace WizardsGrimoireExt\Cards\Base_2;

use BgaSystemException;
use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\Players;

class DanceOfPain extends BaseCard {

    public function castSpell($args) {
        // Deal 3 damage. Discard or gain mana until you have 2 mana cards in your hand
        $this->dealDamage(3);

        $hand_count = ManaCard::getHandCount();

        if ($hand_count == 2) {
            //Do nothing
        } else if ($hand_count < 2) {
            $this->drawManaCards(2 - $hand_count);
        } else {
            $card_ids = explode(",", array_shift($args));
            if (sizeof($card_ids) != ($hand_count - 2)) {
                $nbr_to_discard = $hand_count - 2;
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
