<?php

namespace WizardsGrimoireExt\Core;

use Bga\Games\wizardsgrimoireext\Game;
use BgaUserException;
use WizardsGrimoireExt\Core\Players;
use WizardsGrimoireExt\Objects\CardLocation;

class SpellCard {

    public static function get($card_id) {
        return Game::get()->deck_spells->getCard($card_id);
    }

    public static function getCardInfo($card) {
        return Game::get()->card_types[$card['type']];
    }

    public static function getPlayerId($spell) {
        $info = explode('_', $spell['location']);
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

    /**
     * @return \WizardsGrimoireExt\Cards\BaseCard
     */
    public static function getInstanceOfCard($card) {
        // Get info of the card
        $card_type = Game::get()->card_types[$card['type']];
        // Create the class for the card logic
        $className = "WizardsGrimoireExt\\Cards\\" . $card_type['icon'] . "\\" . $card_type['class'];
        /** @var \WizardsGrimoireExt\Cards\BaseCard */
        $cardClass = new $className();
        $cardClass->id = intval($card['id']);
        return $cardClass;
    }

    public static function getName($card) {
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
            throw new \BgaSystemException("The card is not in the spell pool (" . $spell_id . ")");
        }

        return $card;
    }

    public static function isInRepertoire(int $card_id, int $player_id = 0) {
        if ($player_id == 0) {
            $player_id = Players::getPlayerId();
        }
        $card = SpellCard::get($card_id);

        if ($card['location'] != CardLocation::PlayerSpellRepertoire($player_id)) {
            throw new \BgaSystemException("You don't own the card " . $card_id);
        }

        return $card;
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
        if ($player_id == 0) {
            $player_id = Players::getPlayerId();
        }
        $position = SpellCard::getPositionInRepertoire($spell);

        // Discard all mana on the relic
        $manas = ManaCard::getCardsOnManaCoolDown($position, $player_id);
        foreach ($manas as $mana_id => $mana) {
            ManaCard::addOnTopOfDiscard($mana_id);
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
                throw new BgaUserException("Invalid move");
        }

        // Choose spell
        Game::get()->deck_spells->moveCard(
            $new_spell['id'],
            CardLocation::PlayerSpellRepertoire($player_id),
            $old_spell['location_arg']
        );

        $card = SpellCard::get($new_spell['id']);
        Notifications::chooseSpell($player_id, $card);
        Stats::replaceSpell($player_id, $card);

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

    public static function addNewSpell($new_spell) {
        $player_id = Players::getPlayerId();

        $position = self::getFirstAvailableSpellPosition($player_id);

        // Choose spell
        Game::get()->deck_spells->moveCard(
            $new_spell['id'],
            CardLocation::PlayerSpellRepertoire($player_id),
            $position
        );

        $card = SpellCard::get($new_spell['id']);
        Notifications::chooseSpell($player_id, $card);
        Stats::replaceSpell($player_id, $card);

        $newSpell = Game::get()->deck_spells->pickCardForLocation(
            CardLocation::Deck(),
            CardLocation::SpellSlot(),
            $new_spell['location_arg'],
        );
        Globals::setLastAddedSpell($newSpell['id']);

        Notifications::refillSpell($player_id, $newSpell);
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
        $ongoingSpellActive = self::getOngoingSpells($player_id);
        foreach ($ongoingSpellActive as $spell) {
            $instance = self::getInstanceOfCard($spell);
            if ($instance instanceof \WizardsGrimoireExt\Cards\Shifting_Sand_1\GlassShield) {
                return $instance->isActive();
            }
        }
        return false;
    }
}
