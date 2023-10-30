<?php

namespace WizardsGrimoire\Cards\Sand_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class InvisibleFriend extends BaseCard {

    public function castSpell($args) {
        // Reveal 6 mana cards from the mana deck. Place the highest power mana back on the top of the mana deck. Gain the remaining 5 mana
        $cards = ManaCard::revealFromDeck(6);

        usort($cards, function ($card1, $card2) {
            return ManaCard::getPower($card1) < ManaCard::getPower($card2) ? 1 : -1;
        });

        $topCard = array_shift($cards);
        ManaCard::addCardsToHand($cards);

        ManaCard::addOnTopOfDeck($topCard['id']);
        Notifications::moveManaCard(Players::getPlayerId(), [$topCard]);
    }
}
