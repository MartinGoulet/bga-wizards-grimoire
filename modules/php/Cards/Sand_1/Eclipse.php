<?php

namespace WizardsGrimoire\Cards\Sand_1;

use BgaUserException;
use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Events;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class Eclipse extends BaseCard {

    public function castSpell($args) {
        // Reveal 3 mana cards from the mana deck, place them on any mix of your spells or your opponent's spells
        ManaCard::revealFromDeck(3);
    }

    public function castSpellInteraction($args) {
        $mana_ids = explode(",", array_shift($args));
        $player_ids = explode(",", array_shift($args));
        $mana_cooldown_positions = explode(",", array_shift($args));

        if (sizeof($mana_ids) != 3 || sizeof($player_ids) != 3 || sizeof($mana_cooldown_positions) != 3) {
            throw new BgaUserException("Need to select 3 mana cards");
        }

        $cards = [];
        $cards_opponent = [];
        for ($i = 0; $i < sizeof($mana_ids); $i++) {
            $mana_id = $mana_ids[$i];
            $player_id = $player_ids[$i];
            $position = $mana_cooldown_positions[$i];

            if ($player_id == Players::getPlayerId()) {
                $cards[] = ManaCard::isInReveleadMana($mana_id);
            } else {
                $cards_opponent[] = ManaCard::isInReveleadMana($mana_id);
            }

            ManaCard::addOnTopOfManaCoolDown($mana_id, $position, $player_id);
            Events::onAddManaUnderSpell($player_id, $position);
        }

        if (sizeof($cards) > 0) {
            Notifications::moveManaCard(Players::getPlayerId(), $cards);
        }
        if (sizeof($cards_opponent) > 0) {
            Notifications::moveManaCard(Players::getOpponentId(), $cards_opponent, true);
        }
    }
}
