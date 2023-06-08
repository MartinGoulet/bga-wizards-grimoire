<?php

namespace WizardsGrimoire\Cards;

use BgaSystemException;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

abstract class BaseCard {

    /** @var int */
    public $owner;

    public function castSpell($args) {
        throw new BgaSystemException('Not implemented : castSpell of ' . get_class($this));
    }

    public function onAfterBasicAttack($mana_id) {

    }

    protected function dealDamage(int $damage, int $opponent_id = -1) {

        if($opponent_id <= 0) {
            $opponent_id = Players::getOpponentId();
        }

        $life = Players::getPlayerLife($opponent_id);
        $life_remaining = $life - $damage;
        Players::setPlayerLife($opponent_id, $life_remaining);

        Notifications::receiveDamageFromCard(
            $this->getCardName(),
            $opponent_id,
            $damage,
            $life_remaining
        );
    }

    protected function drawManaCards(int $nbr, int $player_id = 0) 
    {
        if($player_id == 0) {
            $playerId = Players::getPlayerId();
        }
        $mana_cards = Game::get()->deck_manas->pickCards($nbr, CardLocation::Deck(), $playerId);

        Notifications::drawManaCards($playerId, $mana_cards);

        return $mana_cards;
    }

    protected function getCardName() {
        $classParts = explode('\\', get_class($this));
        $class_name = array_pop($classParts);
        $name = array_values(
            array_filter(Game::get()->card_types, function ($card) use ($class_name) {
                return array_key_exists('class', $card) && $card['class'] == $class_name;
            })
        )[0]['name'];
        return $name;
    }
}
