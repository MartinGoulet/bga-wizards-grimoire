<?php

namespace WizardsGrimoire\Core;

use Bga\Games\WizardsGrimoire\Game;
use WizardsGrimoire\Cards\Base_2\SecretOath;
use WizardsGrimoire\Cards\KickStarter_1\Lullaby;
use WizardsGrimoire\Cards\OngoingBaseCard;

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
        if (empty($spell)) return;

        $card_type = SpellCard::getCardInfo($spell);
        switch ($card_type['activation']) {
            case WG_SPELL_ACTIVATION_DELAYED:
                $instance = SpellCard::getInstanceOfCard($spell);

                if ($instance->isDelayedSpellTrigger()) {
                    if ($card_type['activation_auto'] == true) {
                        $instance->castSpell($mana_card);
                        Game::get()->triggerOnAfterDiscardManaFromSpell($instance, $mana_card['id']);
                    } else {
                        $card_ids = Globals::getCoolDownDelayedSpellIds();
                        $card_ids[] = $spell['id'];
                        Globals::setCoolDownDelayedSpellIds($card_ids);
                    }
                }
                break;
                // case WG_SPELL_ACTIVATION_ONGOING:
                //     $instance = SpellCard::getInstanceOfCard($spell);
                //     $count = ManaCard::countOnTopOfManaCoolDown($position, $player_id);
                //     $instance->isActive();
                //     break;
        }

        if ($mana_card['type'] == 5 && $mana_card['type_arg'] == 1) {
            ManaCard::delete($mana_card['id']);
        }
    }

    public static function onAddManaUnderSpell($player_id, $position) {
        $spell = SpellCard::getFromRepertoire($position, $player_id);
        $card_type = SpellCard::getCardInfo($spell);
        if ($card_type['activation'] == WG_SPELL_ACTIVATION_ONGOING) {
            /** @var OngoingBaseCard $instance */
            $instance = SpellCard::getInstanceOfCard($spell);
            $instance->isActive();
        }
    }

    public static function onManaPickedUpUnderSpell($position, $player_id = 0) {
        if ($player_id == 0) {
            $player_id = Players::getPlayerId();
        } else if ($player_id == Players::getOpponentId()) {
            Game::get()->undoSavepoint();
        }
        $spell = SpellCard::getFromRepertoire($position, $player_id);
        $card_type = SpellCard::getCardInfo($spell);
        switch ($card_type['activation']) {
            case WG_SPELL_ACTIVATION_ONGOING:
                /** @var OngoingBaseCard $instance */
                $instance = SpellCard::getInstanceOfCard($spell);
                $instance->isActive();
                break;
        }
    }
}
