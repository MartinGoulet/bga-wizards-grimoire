<?php

namespace WizardsGrimoire\Cards;

use BgaSystemException;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\Stats;

abstract class BaseCard {

    public $card_name = null;

    public function castSpell($args) {
        throw new BgaSystemException('Not implemented : castSpell of ' . get_class($this));
    }

    public function castSpellInteraction($args) {
        throw new BgaSystemException('Not implemented : castSpellCallback of ' . get_class($this));
    }

    public function isOngoingSpellActive(bool $value, int $player_id) {
    }

    public function isDelayedSpellTrigger() {
        return true;
    }

    protected function dealDamage(int $damage, int $opponent_id = -1, bool $recordDamage = true) {

        if ($opponent_id <= 0) {
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

        Globals::setPreviousSpellDamage($damage);
        if($recordDamage) {
            Stats::damageWithSpell($damage, $opponent_id, $this->getCard());
        }
    }

    protected function drawManaCards(int $nbr, int $player_id = 0) {
        return ManaCard::draw($nbr, $player_id, $this->getCardName());
    }

    protected function getCardName() {
        if($this->card_name !== null) return $this->card_name;
        return $this->getCardNameFromType();
    }

    protected function getCardNameFromType() {
        $classParts = explode('\\', get_class($this));
        $class_name = array_pop($classParts);
        $name = array_values(
            array_filter(Game::get()->card_types, function ($card) use ($class_name) {
                return array_key_exists('class', $card) && $card['class'] == $class_name;
            })
        )[0]['name'];
        return $name;
    }

    protected function getCard() {
        $classParts = explode('\\', get_class($this));
        $class_name = array_pop($classParts);
        $card_types = array_filter(Game::get()->card_types, function ($card) use ($class_name) {
            return array_key_exists('class', $card) && $card['class'] == $class_name;
        });
        $types = array_keys($card_types);
        $type = array_shift($types);
        $cards = Game::get()->deck_spells->getCardsOfType($type);
        return array_shift($cards);
    }
}
