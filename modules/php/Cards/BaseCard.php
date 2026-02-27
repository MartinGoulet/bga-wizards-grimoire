<?php

namespace WizardsGrimoireExt\Cards;

use Bga\Games\wizardsgrimoireext\Game;
use BgaSystemException;
use WizardsGrimoireExt\Core\Globals;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\Players;
use WizardsGrimoireExt\Core\SpellCard;
use WizardsGrimoireExt\Core\Stats;

abstract class BaseCard {

    public int $id = 0;

    public $card_name = null;

    public function castSpell($args) {
        throw new BgaSystemException('Not implemented : castSpell of ' . get_class($this));
    }

    public function castSpellInteraction($args) {
        throw new BgaSystemException('Not implemented : castSpellCallback of ' . get_class($this));
    }

    public function isDelayedSpellTrigger() {
        return true;
    }

    protected function healPlayer(int $heal, int $player_id = 0) {

        if ($player_id <= 0) {
            $player_id = Players::getPlayerId();
        }

        $life = Players::getPlayerLife($player_id);
        $life_remaining = $life + $heal;
        Players::setPlayerLife($player_id, $life_remaining);

        Notifications::healFromCard(
            $this->getCardName(),
            $player_id,
            $heal,
            $life_remaining
        );
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
        $info = Globals::getNumberOfCardDrawByCardEffectThisTurn();
        if(!isset($info[$this->id])) {
            $info[$this->id] = 0;
        }
        $cards = ManaCard::draw($nbr, $player_id, $this->getCardName());
        $info[$this->id] += count($cards);
        Globals::setNumberOfCardDrawByCardEffectThisTurn($info);
        return $cards;
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
        return SpellCard::get($this->id);
        // $classParts = explode('\\', get_class($this));
        // $class_name = array_pop($classParts);
        // // $card_types = array_filter(Game::get()->card_types, function ($card) use ($class_name) {
        // //     return array_key_exists('class', $card) && $card['class'] == $class_name;
        // // });
        // // $types = array_keys($card_types);
        // // $type = array_shift($types);
        // // $cards = Game::get()->deck_spells->getCardsOfType($type);
        // // return array_shift($cards);
        // return SpellCard::getInstanceOfCardFromClass($class_name);
    }

    public function getOwnerId() {
        // $card = $this->getCard();
        $card = SpellCard::get($this->id);
        return SpellCard::getPlayerId($card);
    }
}
