<?php

namespace WizardsGrimoire\Core;

use BgaSystemException;
use WizardsGrimoire\Objects\CardLocation;

trait ActionTrait {

    //////////////////////////////////////////////////////////////////////////////
    //////////// Player actions
    //////////// 

    /*
        Each time a player is doing some game action, one of the methods below is called.
        (note: each method below must match an input method in nicodemus.action.php)
    */

    public function chooseSpell(int $card_id) {
        $this->checkAction('chooseSpell');

        $card = $this->deck_spells->getCard($card_id);

        if ($card == null) {
            throw new \BgaSystemException('Card is null');
        } else if ($card['location'] !== 'slot') {
            throw new \BgaUserException('Card is not in the spell pool');
        }

        $playerId = intval($this->getActivePlayerId());
        $cardDestination = CardLocation::PlayerSpellRepertoire($playerId);

        $nbr_spells = $this->deck_spells->countCardInLocation($cardDestination);

        if ($nbr_spells >= 6) {
            throw new \BgaSystemException("Use replaceSpell action");
        }

        $this->deck_spells->moveCard(
            $card_id,
            $cardDestination,
            $nbr_spells + 1
        );

        $newSpell = $this->deck_spells->pickCardForLocation(
            CardLocation::Deck(),
            CardLocation::SpellSlot(),
            $card['location_arg']
        );

        $card = $this->deck_spells->getCard($card_id);

        Notifications::chooseSpell($playerId, $card);
        Notifications::refillSpell($playerId, $newSpell);

        $turn_number = Game::get()->getStat(WG_STAT_TURN_NUMBER);
        if ($turn_number <= 3) {
            $this->gamestate->nextState('next_player');
        } else {
            $this->gamestate->nextState('end');
        }
    }

    public function castSpell(int $card_id, $args) {
        $this->checkAction('castSpell');
        $player_id = intval($this->getActivePlayerId());
        // Get the card and verify ownership
        $spell = Assert::isCardInRepertoire($card_id, $player_id);
        $mana_ids = array_shift($args);
        $mana_ids = explode(',', $mana_ids);
        
        $card_type = $this->card_types[$spell['type']];
        if (sizeof($mana_ids) !== $card_type['cost']) {
            throw new BgaSystemException("Not enough mana");
        }

        $mana_cards_before = array_map(function ($mana_id) use ($player_id) {
            return Assert::isCardInHand($mana_id, $player_id);
        }, $mana_ids);

        // Move card to the mana position below the spell
        foreach ($mana_cards_before as $card_id => $card) {
            $this->deck_manas->insertCardOnExtremePosition(
                $card['id'],
                CardLocation::PlayerManaCoolDown($player_id, $spell['location_arg']),
                true
            );
        }

        $mana_cards_after = $this->deck_manas->getCards($mana_ids);
        Notifications::castSpell($player_id, $card_type['name'], $mana_cards_before, $mana_cards_after);

        if ($card_type['activation'] == WG_SPELL_ACTIVATION_INSTANT) {
            $this->executeCard($spell, $args);
        }

        $this->gamestate->nextState("cast");
    }

    public function basicAttack(int $mana_id) {
        $this->checkAction('basicAttack');
        $player_id = Players::getPlayerId();
        $opponent_id = Players::getOpponentId();
        $card = Assert::isCardInHand($mana_id, $player_id);
        $damage = intval($card['type']);

        Game::get()->deck_manas->insertCardOnExtremePosition($mana_id, CardLocation::Discard(), true);
        $card_after = $this->deck_manas->getCard($mana_id);
        Notifications::moveManaCard($player_id, [$card], [$card_after], "", false);

        $life_remaining = Players::dealDamage($damage, $opponent_id);
        Notifications::basicAttack($opponent_id, $damage, $life_remaining);

        $player_ongoing_spells = Game::getOngoingActiveSpells($player_id);
        $opponent_ongoing_spells = Game::getOngoingActiveSpells($opponent_id);

        foreach ($player_ongoing_spells as $card_id => $card) {
            $spell = $this->getInstanceOfCard($card);
            $spell->owner = $player_id;
            $spell->onAfterBasicAttack($mana_id);
        }

        foreach ($opponent_ongoing_spells as $card_id => $card) {
            $spell = $this->getInstanceOfCard($card);
            $spell->owner = $opponent_id;
            $spell->onAfterBasicAttack($mana_id);
        }

        $this->gamestate->nextState('attack');
    }

    public function pass() {
        $this->checkAction('pass');
        $this->gamestate->nextState('pass');
    }

    //////////////////////////////////////////////////////////////////////////////
    //////////// Private method
    //////////// 


    /**
     * @return BaseCard
     */
    private function getInstanceOfCard($card) {
        // Get info of the card
        $card_type = $this->card_types[$card['type']];
        // Create the class for the card logic
        $className = "WizardsGrimoire\\Cards\\" . $card_type['class'];
        /** @var BaseCard */
        $cardClass = new $className();
        return $cardClass;
    }

    private function executeCard($card, $args) {
        $cardClass = $this->getInstanceOfCard($card);
        // Execute the ability of the card
        $cardClass->castSpell($args, $card['id']);
    }
}
