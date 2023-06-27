<?php

namespace WizardsGrimoire\Cards\Base_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class EnergyShield extends BaseCard {

    public function castSpell($args) {
        // Pick up a mana card off 1 of your other spells. Place a mana card from the mana deck on 1 of your opponent's spells.
        $player_deck_position = intval(array_shift($args));
        $opponent_deck_position = intval(array_shift($args));

        $player_card = ManaCard::hasUnderSpell($player_deck_position);
        $player_card_after = ManaCard::drawFromManaCoolDown($player_deck_position);
        Notifications::moveManaCard(Players::getPlayerId(), [$player_card], [$player_card_after]);

        ManaCard::dealFromDeckToManaCoolDown($opponent_deck_position, Players::getOpponentId());
    }
}
