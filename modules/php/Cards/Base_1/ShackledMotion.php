<?php

namespace WizardsGrimoire\Cards\Base_1;

use BgaSystemException;
use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class ShackledMotion extends BaseCard {

    public function castSpell($args) {
        // Choose 1: Gain 4 mana cards, or your opponent must discard their hand
        $choice = intval(array_shift($args));

        switch ($choice) {
            case 1:
                $this->drawManaCards(4);
                break;
            case 2:
                $opponent_id = Players::getOpponentId();
                $cards_before = ManaCard::getHand($opponent_id);
                foreach ($cards_before as $card_id => $card) {
                    ManaCard::addOnTopOfDiscard($card_id);
                }
                Notifications::moveManaCard($opponent_id, $cards_before,  false);
                Game::undoSavepoint();
                break;

            default:
                throw new BgaSystemException("Invalid choice " . $choice);
        }
    }
}
