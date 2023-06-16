<?php

namespace WizardsGrimoire\Cards\Base_1;

use BgaSystemException;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class Rejuvenation extends BaseCard {

    public function castSpell($args) {
        // Choose 1: Gain 4 mana cards, or take 2 mana cards of any power from the discard pile
        if (sizeof($args) == 0) {
            $this->drawManaCards(4);
        } else if (sizeof($args) == 1) {
            $player_id = Players::getPlayerId();
            $mana_ids = explode(",", array_shift($args));
            $cards_before = array_map(function ($mana_id) {
                return ManaCard::isInDiscard($mana_id);
            }, $mana_ids);

            Game::get()->deck_manas->moveCards($mana_ids, CardLocation::Hand(), $player_id);
            $cards_after = ManaCard::getCards($mana_ids);
            Notifications::moveManaCard($player_id, $cards_before, $cards_after);
        } else {
            throw new BgaSystemException("Arguments error " . sizeof($args));
        }
    }
}
