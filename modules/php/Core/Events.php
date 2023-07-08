<?php

namespace WizardsGrimoire\Core;

use WizardsGrimoire\Cards\Base_2\SecretOath;

/*
 * Events: handle events
 */

class Events {

    public static function onIsActiveGrowthChanged() {
        self::checkSecretOathHand();
    }

    public static function onIsActiveSecretOathChanged() {
        self::checkSecretOathHand();
    }

    public static function onManaDiscarded($mana_card, int $position, int $player_id = 0) {
        if ($player_id == 0) {
            $player_id = Players::getPlayerId();
        }
        $spell = SpellCard::getFromRepertoire($position, $player_id);
        $card_type = SpellCard::getCardInfo($spell);
        if ($card_type['activation'] == WG_SPELL_ACTIVATION_DELAYED) {
            $instance = SpellCard::getInstanceOfCard($spell);
            if ($card_type['activation_auto'] == true) {
                $instance->castSpell($mana_card);
            } else if ($instance->isDelayedSpellTrigger()) {
                $card_ids = Globals::getCoolDownDelayedSpellIds();
                $card_ids[] = $spell['id'];
                Globals::setCoolDownDelayedSpellIds($card_ids);
            }
        }
    }

    public static function onManaDrawed($cards) {
        if (Globals::getIsActiveSecretOath() && Players::getOpponentId() == Globals::getIsActiveSecretOathPlayer()) {
            return SecretOath::check($cards);
        }
        return $cards;
    }

    public static function onAddCardToHand() {
        self::checkSecretOathHand();
    }

    public static function onPlayerNewTurn() {
        self::checkSecretOathHand();
    }

    private static function checkSecretOathHand() {
        if (Globals::getIsActiveSecretOath()) {
            // $player_id = Globals::getIsActiveGrowthPlayer();
            $opponent_id = Players::getOpponentIdOf(Globals::getIsActiveSecretOathPlayer());
            SecretOath::check(ManaCard::getHand($opponent_id));
        }
    }
}
