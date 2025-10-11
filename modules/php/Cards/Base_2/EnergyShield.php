<?php

namespace WizardsGrimoireExt\Cards\Base_2;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\Events;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\Players;

class EnergyShield extends BaseCard {

    public function castSpell($args) {
        // Pick up a mana card off 1 of your other spells. Place a mana card from the mana deck on 1 of your opponent's spells.
        $player_deck_position = intval(array_shift($args));
        $opponent_deck_position = intval(array_shift($args));

        ManaCard::hasUnderSpell($player_deck_position);
        $card = ManaCard::drawFromManaCoolDown($player_deck_position);
        Notifications::moveManaCard(Players::getPlayerId(), [$card]);
        Events::onManaPickedUpUnderSpell($player_deck_position);

        ManaCard::dealFromDeckToManaCoolDown($opponent_deck_position, Players::getOpponentId());
    }
}
