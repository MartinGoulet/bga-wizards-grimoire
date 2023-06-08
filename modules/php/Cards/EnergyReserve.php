<?php

namespace WizardsGrimoire\Cards;

use BgaSystemException;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class EnergyReserve extends BaseCard {

    public function castSpell($args) {
        // Pick up a mana card off 1 of your others spells. Gain mana equal to that mana's power.
        $player_id = Players::getPlayerId();
        $position = intval(array_shift($args));
        $card = Game::get()->deck_manas->getCardOnTop(CardLocation::PlayerManaCoolDown($player_id, $position));

        if ($card == null) {
            throw new BgaSystemException("No card");
        }

        Game::get()->deck_manas->moveCard($card['id'], CardLocation::Hand(), Players::getPlayerId());
        $card_after = Game::get()->deck_manas->getCard($card['id']);
        Notifications::moveManaCard(Players::getPlayerId(), [$card], [$card_after]);

        $mana_power = intval($card['type']);
        $this->drawManaCards($mana_power);
    }
}
