<?php

namespace WizardsGrimoire\Cards;

use BgaSystemException;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

abstract class BaseCard {

    /** @var int */
    public $owner;

    public function castSpell($args) {
        throw new BgaSystemException('Not implemented : castSpell of ' . get_class($this));
    }

    public function castSpellInteraction($args) {
        throw new BgaSystemException('Not implemented : castSpellCallback of ' . get_class($this));
    }

    public function isOngoingSpellActive(bool $value) {
        
    }

    public function onManaDiscard($mana, $spell) {
        throw new BgaSystemException('Not implemented : onManaDiscard of ' . get_class($this));
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
        return ManaCard::Draw($nbr, $player_id);
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
