<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\Assert;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class ToxicGift extends BaseCard {

    public function castSpell($args) {
        // Give your opponent a mana card from your hand. If you do, deal damage equal to its power
        if(sizeof($args) !== 1) {
            return;
        }
        $card_id = intval(array_shift($args));
        $card = Assert::isCardInHand($card_id);

        $card_after = Game::get()->moveCard($card["id"], CardLocation::Hand(), Players::getOpponentId());
        Notifications::moveManaCard(Players::getPlayerId(), [$card], [$card_after]);

        $damage = intval($card['type']);
        $this->dealDamage($damage);
    }

}
