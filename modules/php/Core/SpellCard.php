<?php

namespace WizardsGrimoire\Core;

use Bga\GameFramework\UserException;
use Bga\GameFramework\VisibleSystemException;
use Bga\Games\WizardsGrimoire\Game;
use WizardsGrimoire\Cards\OngoingBaseCard;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class SpellCard {

    public static function get($card_id) {
        return Game::get()->deck_spells->getCard($card_id);
    }

    public static function getCardInfo($card) {
        return Game::get()->card_types[$card['type']];
    }

    public static function getPlayerId(array $spell) {
        $info = explode('_', $spell['location']);
        if (count($info) == 1) {
            return 0;
        }
        return intval(array_pop($info));
    }

    public static function getPositionInRepertoire($spell) {
        return intval($spell['location_arg']);
    }

    public static function getFromRepertoire($position, int $player_id = 0) {
        if ($player_id == 0) {
            $player_id = Players::getPlayerId();
        }
        $cards = Game::get()->deck_spells->getCardsInLocation(CardLocation::PlayerSpellRepertoire($player_id), $position);
        return array_shift($cards);
    }

    public static function getCardsFromRepertoire(int $player_id = 0) {
        if ($player_id == 0) {
            $player_id = Players::getPlayerId();
        }
        return Game::get()->deck_spells->getCardsInLocation(CardLocation::PlayerSpellRepertoire($player_id));
    }

    public static function getCardsFromDiscardPile() {
        return Game::get()->deck_spells->getCardsInLocation(CardLocation::Discard());
    }

    /**
     * @return \WizardsGrimoire\Cards\BaseCard
     */
    public static function getInstanceOfCard($card) {
        // Get info of the card
        $card_type = Game::get()->card_types[$card['type']];
        // Create the class for the card logic
        $className = "WizardsGrimoire\\Cards\\" . $card_type['icon'] . "\\" . $card_type['class'];
        /** @var \WizardsGrimoire\Cards\BaseCard */
        $cardClass = new $className();
        $cardClass->id = intval($card['id']);
        return $cardClass;
    }

    public static function getInstanceOfCardFromClass(string $className) {
        $classInfos = explode("\\", $className);
        $className = array_pop($classInfos);
        $card_types = array_filter(Game::get()->card_types, function ($card) use ($className) {
            return array_key_exists('class', $card) && $card['class'] == $className;
        });
        $types = array_keys($card_types);
        $type = array_shift($types);
        $cards = Game::get()->deck_spells->getCardsOfType($type);
        $card = array_shift($cards);
        if ($card === null) {
            return null;
        }
        return self::getInstanceOfCard($card);
    }

    public static function getName(array $card) {
        if (empty($card)) {
            throw new UserException("Card not found");
        }
        $card_type = Game::get()->card_types[$card['type']];
        return $card_type['name'];
    }

    public static function getOngoingActiveSpells($player_id) {
        $spells = self::getCardsFromRepertoire($player_id);
        $ongoing_active_spell = array_filter($spells, function ($card) use ($player_id) {
            $card_type = self::getCardInfo($card);
            return $card_type['activation'] == WG_SPELL_ACTIVATION_ONGOING
                && ManaCard::countOnTopOfManaCoolDown($card['location_arg']) > 0;
        });
        return $ongoing_active_spell;
    }

    public static function getOngoingSpells($player_id) {
        $spells = self::getCardsFromRepertoire($player_id);
        $ongoing_spell = array_filter($spells, function ($card) use ($player_id) {
            $card_type = self::getCardInfo($card);
            return $card_type['activation'] == WG_SPELL_ACTIVATION_ONGOING;
        });
        return $ongoing_spell;
    }

    public static function getDelayedActiveSpells($player_id) {
        $spells = SpellCard::getCardsFromRepertoire($player_id);
        $ongoing_active_spell = array_filter($spells, function ($card) use ($player_id) {
            $card_type = self::getCardInfo($card);
            return $card_type['activation'] == WG_SPELL_ACTIVATION_DELAYED
                && ManaCard::countOnTopOfManaCoolDown($card['location_arg']) > 0;
        });
        return $ongoing_active_spell;
    }

    public static function isInPool(int $spell_id) {
        $card = SpellCard::get($spell_id);

        if ($card['location'] != CardLocation::SpellSlot()) {
            throw new VisibleSystemException("The card is not in the spell pool (" . $spell_id . ")");
        }

        return $card;
    }

    public static function isSpellInRepertoire(array $spell) {
        $info = explode('_', $spell['location']);
        if (count($info) < 2) {
            return false;
        }
        return $info[0] == "spr";
    }

    public static function isInRepertoire(int $card_id, int $player_id = 0) {
        if ($player_id == 0) {
            $player_id = Players::getPlayerId();
        }
        $card = SpellCard::get($card_id);

        if ($card['location'] != CardLocation::PlayerSpellRepertoire($player_id)) {
            throw new VisibleSystemException("You {" . $player_id . "} don't own the card " . $card_id);
        }

        return $card;
    }

    public static function isInRepertoireBool(int $card_id, int $player_id = 0) {
        try {
            self::isInRepertoire($card_id, $player_id);
            return true;
        } catch (VisibleSystemException $e) {
            return false;
        }
    }

    public static function destroyRelic(array $spell, string $destination = "discard") {
        $player_id = Players::getPlayerId();

        self::discardAllManaCardsOnSpell($spell, $player_id);

        // Discard spell
        Game::get()->deck_spells->insertCardOnExtremePosition($spell['id'], $destination, true);
        $discarded_card = SpellCard::get($spell['id']);
        Notifications::destroySpell($player_id, $discarded_card, $destination);
    }

    public static function discardAllManaCardsOnSpell(array $spell, int $player_id = 0) {
        $position = SpellCard::getPositionInRepertoire($spell);
        self::discardAllManaCardsInPosition($spell, $position, $player_id);
    }

    public static function discardAllManaCardsInPosition(array $spell, int $position, int $player_id = 0) {
        if ($player_id == 0) {
            $player_id = Players::getPlayerId();
        }

        // Discard all mana on the relic
        $instance = SpellCard::getInstanceOfCard($spell);
        $manas = ManaCard::getCardsOnManaCoolDown($position, $player_id);
        foreach ($manas as $mana_id => $mana) {
            ManaCard::addOnTopOfDiscard($mana_id);
            Game::get()->triggerOnAfterDiscardManaFromSpell($instance, $mana_id);
        }
        if (count($manas) > 0) {
            Notifications::discardManaCards($player_id, $manas);
        }
    }

    public static function replaceSpell($old_spell, $new_spell, $move = "replace", $player_id = 0) {
        if ($player_id == 0) {
            $player_id = Players::getPlayerId();
        }

        // Discard old spell
        Game::get()->deck_spells->insertCardOnExtremePosition($old_spell['id'], CardLocation::Discard(), true);
        $discarded_card = SpellCard::get($old_spell['id']);
        switch ($move) {
            case "replace":
            case "replaceSeeingStone":
                Notifications::discardSpell($player_id, $discarded_card);
                break;
            case "destroy":
                Notifications::destroySpell($player_id, $discarded_card, 'discard');
                break;
            default:
                throw new UserException("Invalid move");
        }

        // Choose spell
        Game::get()->deck_spells->moveCard(
            $new_spell['id'],
            CardLocation::PlayerSpellRepertoire($player_id),
            $old_spell['location_arg']
        );

        $playedSpellsIds = Globals::getPlayedSpellIdsThisGame($player_id);
        $playedSpellsIds[] = $new_spell['id'];
        Globals::setPlayedSpellIdsThisGame($player_id, $playedSpellsIds);

        $card = SpellCard::get($new_spell['id']);
        Notifications::chooseSpell($player_id, $card);
        Stats::replaceSpell($player_id, $card);

        if($player_id == Players::getPlayerId()) {
            Game::get()->triggerOnAddSpellToRepertoire($card);
        }

        if ($move !== "replaceSeeingStone") {
            $newSpell = Game::get()->deck_spells->pickCardForLocation(
                CardLocation::Deck(),
                CardLocation::SpellSlot(),
                $new_spell['location_arg'],
            );
            Globals::setLastAddedSpell($newSpell['id']);

            Notifications::refillSpell($player_id, $newSpell);
        }
        Game::get()->undoSavepoint();
    }

    public static function addNewSpell($new_spell, bool $replaceSpellPool = true) {
        $player_id = Players::getPlayerId();

        $position = self::getFirstAvailableSpellPosition($player_id);

        $card = SpellCard::get($new_spell['id']);
        $newSpellLocation = $card['location'];

        // Choose spell
        Game::get()->deck_spells->moveCard(
            $new_spell['id'],
            CardLocation::PlayerSpellRepertoire($player_id),
            $position
        );

        $playedSpellsIds = Globals::getPlayedSpellIdsThisGame($player_id);
        $playedSpellsIds[] = $new_spell['id'];
        Globals::setPlayedSpellIdsThisGame($player_id, $playedSpellsIds);

        $card = SpellCard::get($new_spell['id']);
        if($newSpellLocation == CardLocation::SpellSlot()) {
            Notifications::chooseSpell($player_id, $card);
        } else {
            Notifications::chooseSpellFromDiscard($player_id, $card);
        }
        Stats::replaceSpell($player_id, $card);

        Game::get()->triggerOnAddSpellToRepertoire($new_spell);
        
        if($replaceSpellPool) {
            $newSpell = Game::get()->deck_spells->pickCardForLocation(
                CardLocation::Deck(),
                CardLocation::SpellSlot(),
                $new_spell['location_arg'],
            );
            Globals::setLastAddedSpell($newSpell['id']);
            Notifications::refillSpell($player_id, $newSpell);
        }

        Game::get()->undoSavepoint();
    }

    public static function getFirstAvailableSpellPosition(int $player_id): int {
        $spells = self::getCardsFromRepertoire($player_id);
        $positions = array_map(fn($s) => SpellCard::getPositionInRepertoire($s), $spells);
        sort($positions);
        $emptyPositions = array_diff(range(1, 6), $positions);
        $position = intval(array_shift($emptyPositions));
        return $position;
    }

    public static function isActiveGlassShield(int $player_id): bool {
        $opponentId = Players::getOpponentIdOf($player_id);
        $ongoingSpellActive = self::getOngoingSpells($opponentId);
        foreach ($ongoingSpellActive as $spell) {
            $instance = self::getInstanceOfCard($spell);
            if ($instance instanceof \WizardsGrimoire\Cards\Shifting_Sand_1\GlassShield) {
                return $instance->isActive();
            }
        }
        return false;
    }

    public static function isActiveOngoingSpell(string $className) : bool {
        /** @var OngoingBaseCard $card */
        $card = self::getInstanceOfCardFromClass($className);
        return $card !== null && $card->isActive();
    }

    public static function getDiscard() : array {
        return Game::get()->deck_spells->getCardsInLocation(CardLocation::Discard());
    }
}
