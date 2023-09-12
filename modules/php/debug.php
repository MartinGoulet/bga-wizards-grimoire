<?php

namespace WizardsGrimoire;

use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

trait DebugTrait {

    public function refillSpells() {
        $cards = Game::get()->deck_spells->getCardsInLocation(CardLocation::SpellSlot());

        foreach ($cards as $card_id => $card) {
            Game::get()->deck_spells->insertCardOnExtremePosition($card['id'], CardLocation::Discard(), true);
            Game::get()->deck_spells->pickCardForLocation(
                CardLocation::Deck(),
                CardLocation::SpellSlot(),
                $card['location_arg'],
            );
        }
    }

    public function drawManaCards($count) {
        ManaCard::draw($count);
    }

    public function drawManaCardsOpponent($count) {
        ManaCard::draw($count, Players::getOpponentId());
    }

    public function resetLife() {
        Players::setPlayerLife(Players::getPlayerId(), 100);
        Players::setPlayerLife(Players::getOpponentId(), 100);
    }

    public function shuffleSpells() {
        Game::get()->deck_spells->moveAllCardsInLocation(CardLocation::Discard(), CardLocation::Deck());
        Game::get()->deck_spells->shuffle(CardLocation::Deck());
    }

    public function setupGameDebug() {
        $spell_deck = Game::get()->deck_spells;
        $mana_deck = Game::get()->deck_manas;

        $spell_deck->moveAllCardsInLocation(null, CardLocation::Deck());
        $mana_deck->moveAllCardsInLocation(null, CardLocation::Deck());
        $mana_deck->shuffle(CardLocation::Deck());

        Globals::setPreviousBasicAttackPower(2);

        $players_spell_cards = [
            "2329672" => ["TimeDistortion", "Windstorm", "BadFortune", "Possessed", "SilentSupport", "TouchTheVoid"],
            "2329673" => ["FalseFace", "Wrath", "WildBloom", "Hoodwink", "StoneCrush", "EnergyReserve"],
        ];

        $players_spell_mana = [
            "2329672" => [0, 0, 0, 3, 0, 2],
            "2329673" => [0, 0, 1, 1, 2, 2],
        ];

        foreach ($players_spell_cards as $player_id => $cards) {
            $index = 0;
            foreach ($cards as $name) {
                $index += 1;
                $card = $this->getCardByClassName($name);
                if ($card !== null) {
                    $spell_deck->moveCard($card['id'], CardLocation::PlayerSpellRepertoire($player_id), $index);
                } else {
                    var_dump("Wrong card name : " . $name);
                }
            }
        }

        foreach ($players_spell_mana as $player_id => $nbr_cards) {
            $index = 0;
            foreach ($nbr_cards as $nbr_card) {
                $index += 1;
                for ($i = 0; $i < $nbr_card; $i++) {
                    ManaCard::dealFromDeckToManaCoolDown($index, $player_id);
                }
            }
        }

        $spell_deck->shuffle(CardLocation::Deck());

        for ($i = 1; $i <= intval(self::getGameStateValue(WG_VAR_SLOT_COUNT)); $i++) {
            $this->deck_spells->pickCardForLocation(CardLocation::Deck(), CardLocation::SpellSlot(), $i);
        }

        
        $mana_deck->pickCards(10, CardLocation::Deck(), "2329672");
        $mana_deck->pickCards(10, CardLocation::Deck(), "2329673");
        Players::setPlayerLife("2329672", 100);
        Players::setPlayerLife("2329673", 100);

        Globals::setIsActiveBattleVision(true, 2329672);
        Globals::setIsActiveGrowth(false, 0);
        Globals::setIsActiveLullaby(false, 0);
        Globals::setIsActivePowerHungry(false, 0);
        Globals::setIsActivePuppetmaster(false, 0);
        Globals::setIsActiveSecretOath(false, 0);
        Game::undoSavepoint();
    }

    public function addManaDiscardPile() {
        $mana_deck = Game::get()->deck_manas;
        $mana_deck->pickCardsForLocation(5, CardLocation::Deck(), CardLocation::Discard());
    }

    private function getCardByClassName($class_name) {
        $card_types = array_filter(Game::get()->card_types, function ($card) use ($class_name) {
            return array_key_exists('class', $card) && $card['class'] == $class_name;
        });
        $types = array_keys($card_types);
        $type = array_shift($types);
        $cards = Game::get()->deck_spells->getCardsOfType($type);
        return array_shift($cards);
    }
}
