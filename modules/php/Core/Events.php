<?php

namespace WizardsGrimoire\Core;

use WizardsGrimoire\Cards\Base_2\SecretOath;
use WizardsGrimoire\Cards\KickStarter_1\Lullaby;

/*
 * Events: handle events
 */

class Events {

    public static function onCheckOngoingActiveSpell() {
        Lullaby::check();
        SecretOath::check();
    }

    public static function onManaDiscarded($mana_card, int $position, int $player_id = 0) {
        if ($player_id == 0) {
            $player_id = Players::getPlayerId();
        }
        $spell = SpellCard::getFromRepertoire($position, $player_id);
        $card_type = SpellCard::getCardInfo($spell);
        switch ($card_type['activation']) {
            case WG_SPELL_ACTIVATION_DELAYED:
                $instance = SpellCard::getInstanceOfCard($spell);
                if ($instance->isDelayedSpellTrigger()) {
                    if ($card_type['activation_auto'] == true) {
                        $instance->castSpell($mana_card);
                    } else {
                        $card_ids = Globals::getCoolDownDelayedSpellIds();
                        $card_ids[] = $spell['id'];
                        Globals::setCoolDownDelayedSpellIds($card_ids);
                    }
                }
                break;
            case WG_SPELL_ACTIVATION_ONGOING:
                $instance = SpellCard::getInstanceOfCard($spell);
                $count = ManaCard::countOnTopOfManaCoolDown($position, $player_id);
                $instance->isOngoingSpellActive($count > 0, $player_id);
                break;
        }
    }

    public static function onAddManaUnderSpell($player_id, $position) {
        $spell = SpellCard::getFromRepertoire($position, $player_id);
        $card_type = SpellCard::getCardInfo($spell);
        if ($card_type['activation'] == WG_SPELL_ACTIVATION_ONGOING) {
            $instance = SpellCard::getInstanceOfCard($spell);
            $instance->isOngoingSpellActive(true, $player_id);
        }
    }

    public static function onManaPickedUpUnderSpell($position, $player_id = 0) {
        if ($player_id == 0) {
            $player_id = Players::getPlayerId();
        } else if ($player_id == Players::getOpponentId()) {
            Game::undoSavepoint();
        }
        $spell = SpellCard::getFromRepertoire($position, $player_id);
        $card_type = SpellCard::getCardInfo($spell);
        switch ($card_type['activation']) {
            case WG_SPELL_ACTIVATION_ONGOING:
                $instance = SpellCard::getInstanceOfCard($spell);
                $count = ManaCard::countOnTopOfManaCoolDown($position, $player_id);
                $instance->isOngoingSpellActive($count > 0, $player_id);
                break;
        }
    }
}
