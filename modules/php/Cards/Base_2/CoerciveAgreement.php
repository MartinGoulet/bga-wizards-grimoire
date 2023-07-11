<?php

namespace WizardsGrimoire\Cards\Base_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Events;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class CoerciveAgreement extends BaseCard {

    public function castSpell($args) {
        // Choose 1: Take up to 3 randomly selected mana from your opponent's hand, or discard a mana card off 2 of your other spells.
        if ($args == null || $args == "") {
            $opponent_id = Players::getOpponentId();
            $cards = ManaCard::getHand($opponent_id);
            $random_cards = $cards;
            if (sizeof($cards) > 3) {
                shuffle($cards);
                shuffle($cards);
                $keys = array_rand($cards, 3);
                $random_cards = [];
                foreach($keys as $key) {
                    $random_cards[] = $cards[$key];
                }
            }
            $ids = array_map(function ($card) {
                return $card['id'];
            }, $random_cards);
            Game::get()->deck_manas->moveCards($ids, CardLocation::Hand(), Players::getPlayerId());
            Notifications::moveManaCard($opponent_id, $random_cards, false);
            Events::onAddCardToHand();
        } else {
            $values = explode(',', array_shift($args));

            if (sizeof($values) > 0) {
                $position = intval(array_shift($values));
                ManaCard::discardManaFromSpell($position);
            }
            if (sizeof($values) > 0) {
                $position = intval(array_shift($values));
                ManaCard::discardManaFromSpell($position);
            }
        }
    }
}
