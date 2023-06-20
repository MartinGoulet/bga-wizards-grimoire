<?php

namespace WizardsGrimoire\Cards\Base_2;

use WizardsGrimoire\Cards\BaseCard;
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
            if (sizeof($random_cards) > 3) {
                shuffle($random_cards);
                shuffle($random_cards);
                $random_cards = array_rand($cards, 3);
            }
            $ids = array_map(function ($card) {
                return $card['id'];
            }, $random_cards);
            Game::get()->deck_manas->moveCards($ids, CardLocation::Hand(), Players::getPlayerId());
            $cards_after = ManaCard::getCards($ids);
            Notifications::moveManaCard($opponent_id, $random_cards, $cards_after);
        } else {
            $position = intval(array_shift($args));
            ManaCard::discardManaFromSpell($position);
            $position = intval(array_shift($args));
            ManaCard::discardManaFromSpell($position);
        }
    }
}
