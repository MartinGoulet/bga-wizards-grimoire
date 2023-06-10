<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\Assert;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class ToxicGift extends BaseCard {

    public function castSpell($args) {
        // Give your opponent a mana card from your hand. If you do, deal damage equal to its power
        if (sizeof($args) !== 1) {
            Notifications::spellNoEffect();
            return;
        }
        $card_id = intval(array_shift($args));
        $card = Assert::isCardInHand($card_id);

        Game::get()->deck_manas->moveCard($card["id"], CardLocation::Hand(), Players::getOpponentId());
        $card_after = ManaCard::get($card_id);
        Notifications::moveManaCard(Players::getPlayerId(), [$card], [$card_after], "@@@", false);

        $damage = intval($card['type']);
        $this->dealDamage($damage);
    }
}
