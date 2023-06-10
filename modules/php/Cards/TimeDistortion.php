<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\Assert;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class TimeDistortion extends BaseCard {

    public function castSpell($args) {
        $player_id = Players::getPlayerId();
        $mana_cards_id = explode(",",  array_shift($args));
        $mana_cards_before = ManaCard::getCards($mana_cards_id);

        // Verify if card is on top of the spell deck (first card in mana cooldown deck)
        // Note : Since mana card is not moved, the current spell card played didn't have
        //        any mana card under it.
        foreach ($mana_cards_before as $card_id => $card) {
            Assert::isManaCardOnTopOfSpellCard($card, $player_id);
        }

        Game::get()->deck_manas->moveCards($mana_cards_id, CardLocation::Hand(), $player_id);
        // Actualize mana card
        $mana_cards_after = ManaCard::getCards($mana_cards_id);

        Notifications::moveManaCard($player_id, $mana_cards_before, $mana_cards_after);
    }
}
