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

    public function resetLife() {
        Players::setPlayerLife(Players::getPlayerId(), 1);
        Players::setPlayerLife(Players::getOpponentId(), 1);
    }

    public function shuffleSpells() {
        Game::get()->deck_spells->moveAllCardsInLocation(CardLocation::Discard(), CardLocation::Deck());
        Game::get()->deck_spells->shuffle(CardLocation::Deck());
    }

    public function setupGameDebug() {
        $spell_deck = Game::get()->deck_spells;
        $mana_deck = Game::get()->deck_manas;

        $spell_deck->moveAllCardsInLocation(null, CardLocation::Deck());

        $players_spell_cards = [
            "2329672" => ["GleamOfHope", "FatalFlaw", "CoerciveAgreement", "Rejuvenation"],
            "2329673" => ["LivingWind", "TrapAttack", "CrushingBlow"],
        ];

        foreach ($players_spell_cards as $player_id => $cards) {
            $index = 0;
            foreach ($cards as $name) {
                $index += 1;
                $card = $this->getCardByClassName($name);
                if($card !== null) {
                    $spell_deck->moveCard($card['id'], CardLocation::PlayerSpellRepertoire($player_id), $index);
                } else {
                    var_dump("Wrong card name : " . $name);
                }
            }
        }

        $spell_deck->shuffle(CardLocation::Deck());

        for ($i = 1; $i <= intval(self::getGameStateValue(WG_VAR_SLOT_COUNT)); $i++) {
            $this->deck_spells->pickCardForLocation(CardLocation::Deck(), CardLocation::SpellSlot(), $i);
        }

        $mana_deck->moveAllCardsInLocation(null, CardLocation::Deck());
        $mana_deck->shuffle(CardLocation::Deck());
        $mana_deck->pickCards(5, CardLocation::Deck(), "2329672");
        $mana_deck->pickCards(5, CardLocation::Deck(), "2329673");
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
