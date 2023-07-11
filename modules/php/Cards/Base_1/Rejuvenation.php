<?php

namespace WizardsGrimoire\Cards\Base_1;

use BgaSystemException;
use BgaUserException;
use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;

class Rejuvenation extends BaseCard {

    public function castSpell($args) {
        // Choose 1: Gain 4 mana cards, or take 2 mana cards of any power from the discard pile
        if (sizeof($args) == 0) {
            $this->drawManaCards(4);
        } else if (sizeof($args) == 1) {
            $mana_ids = explode(",", array_shift($args));

            if (sizeof($mana_ids) > 2) {
                throw new BgaUserException(Game::get()->translate("You must select only 2 mana cards"));
            } else if (sizeof($mana_ids) > 0) {
                $cards = array_map(function ($mana_id) {
                    return ManaCard::isInDiscard($mana_id);
                }, $mana_ids);
                ManaCard::addCardsToHand($cards);
            } else {
                Notifications::spellNoEffect();
            }
        } else {
            throw new BgaSystemException("Arguments error " . sizeof($args));
        }
    }
}
