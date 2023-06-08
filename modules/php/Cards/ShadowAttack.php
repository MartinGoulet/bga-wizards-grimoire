<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\Assert;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class ShadowAttack extends BaseCard {

    public function castSpell($args) {
        // Discard a mana card off 1 of your other spells. 
        // Deal damage and gain mana equal to that mana's power
        $mana_deck_pos = array_shift($args);
        $player_id = Players::getPlayerId();

        $card = Assert::hasManaCardUnderSpell($mana_deck_pos, $player_id);

        Game::get()->deck_manas->insertCardOnExtremePosition($card['id'], CardLocation::Discard(), true);
        $cardAfter = Game::get()->deck_manas->getCard($card['id']);
        Notifications::moveManaCard($player_id, [$card], [$cardAfter]);

        $power = intval($card['type']);

        $this->dealDamage($power);
        $this->drawManaCards($power);
    }
}
