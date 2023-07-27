<?php

namespace WizardsGrimoire\Core;

use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class SpellCard {

    public static function get($card_id) {
        return Game::get()->deck_spells->getCard($card_id);
    }

    public static function getCardInfo($card) {
        return Game::get()->card_types[$card['type']];
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
     * @return BaseCard
     */
    public static function getInstanceOfCard($card) {
        // Get info of the card
        $card_type = Game::get()->card_types[$card['type']];
        // Create the class for the card logic
        $className = "WizardsGrimoire\\Cards\\" . $card_type['icon'] . "\\" . $card_type['class'];
        /** @var BaseCard */
        $cardClass = new $className();
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
            throw new \BgaSystemException(Game::get()->translate("You don't own the card"));
        }

        return $card;
    }

    public static function replaceSpell($old_spell, $new_spell) {
        $player_id = Players::getPlayerId();

        // Discard old spell
        Game::get()->deck_spells->insertCardOnExtremePosition($old_spell['id'], CardLocation::Discard(), true);
        $discarded_card = SpellCard::get($old_spell['id']);
        Notifications::discardSpell($player_id, $discarded_card);

        // Choose spell
        Game::get()->deck_spells->moveCard(
            $new_spell['id'],
            CardLocation::PlayerSpellRepertoire($player_id),
            $old_spell['location_arg']
        );

        $card = SpellCard::get($new_spell['id']);
        Notifications::chooseSpell($player_id, $card);
        Stats::replaceSpell($player_id, $card);

        $newSpell = Game::get()->deck_spells->pickCardForLocation(
            CardLocation::Deck(),
            CardLocation::SpellSlot(),
            $new_spell['location_arg'],
        );

        Notifications::refillSpell($player_id, $newSpell);
        Game::undoSavepoint();
    }
}
