<?php

namespace WizardsGrimoire\Cards\Base_1;

use WizardsGrimoire\Cards\BaseCard;
use BgaSystemException;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class FriendlyTruce extends BaseCard {

    public function castSpell($args) {
        // Your opponent may give you 3 cards from their hand. 
        // If they do not, gain 5 mana cards
        if (ManaCard::getHandCount(Players::getOpponentId()) < 3) {
            Globals::setSkipInteraction(true);
            $this->drawManaCards(5);
        }
    }

    public function castSpellInteraction($args) {
        if ($args != null && $args != "") {
            $mana_ids = explode(",", array_shift($args));
            if (sizeof($mana_ids) != 3) {
                throw new BgaSystemException("Wrong number of card " . sizeof($mana_ids));
            }
            $opponent_id = Players::getOpponentId();

            $cards_before = array_map(function ($mana_id) use ($opponent_id) {
                return ManaCard::isInHand($mana_id, $opponent_id);
            }, $mana_ids);

            Game::get()->deck_manas->moveCards($mana_ids, CardLocation::Hand(), Players::getPlayerId());
            Notifications::moveManaCard(Players::getPlayerId(), $cards_before, false);
        } else {
            $this->drawManaCards(5);
        }
    }
}
