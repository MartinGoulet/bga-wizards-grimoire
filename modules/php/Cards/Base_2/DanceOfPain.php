<?php

namespace WizardsGrimoire\Cards\Base_2;

use BgaSystemException;
use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

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
            $card_ids = explode(",", $args);
            if (sizeof($card_ids) != ($hand_count - 2)) {
                $nbr_to_discard = $hand_count - 2;
                throw new BgaSystemException("Must discard " . $nbr_to_discard  . " cards");
            }

            $cards = ManaCard::getCards($card_ids);
            $cards_after = [];
            foreach ($cards as $card_id) {
                ManaCard::addOnTopOfDiscard($card_id);
                $cards_after[] =  ManaCard::get($card_id);
            }
            Notifications::moveManaCard(Players::getPlayerId(), $cards, $cards_after);
            
        }
    }
}
