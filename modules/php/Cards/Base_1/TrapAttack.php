<?php

namespace WizardsGrimoire\Cards;

use BgaSystemException;
use BgaUserException;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class TrapAttack extends BaseCard {

    public function castSpell($args) {
        // Pick up a mana card off 1 of your other spells. 
        // Deal damage equal to that mana's power
        $position = intval(array_shift($args));
        $player_id = Players::getPlayerId();

        $card = ManaCard::getOnTopOnManaCoolDown($position);
        if ($card == null) {
            throw new BgaSystemException("Card not found at position " . $position);
        }

        $card_after = Game::get()->deck_manas->pickCardForLocation(
            CardLocation::PlayerManaCoolDown($player_id, $position),
            CardLocation::Hand(),
            $player_id,
        );

        Notifications::moveManaCard($player_id, [$card], [$card_after]);

        $power = intval($card['type']);
        $this->dealDamage($power);
    }
}
