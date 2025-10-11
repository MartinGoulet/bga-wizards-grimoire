<?php

namespace WizardsGrimoireExt\Cards\Base_1;

use WizardsGrimoireExt\Cards\BaseCard;
use BgaSystemException;
use WizardsGrimoireExt\Core\Game;
use WizardsGrimoireExt\Core\Globals;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\Players;
use WizardsGrimoireExt\Objects\CardLocation;

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
            Notifications::giveManaCards(Players::getPlayerId(), $cards_before);
        } else {
            $this->drawManaCards(5);
        }
    }
}
