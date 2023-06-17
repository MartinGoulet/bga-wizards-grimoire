<?php

namespace WizardsGrimoire\Core;

/*
 * Events: handle events
 */

class Events {

    public static function onManaDiscarded($mana_card, int $position, int $player_id = 0) {
        if ($player_id == 0) {
            $player_id = Players::getPlayerId();
        }
        $spell = SpellCard::getFromRepertoire($position, $player_id);
        $card_type = SpellCard::getCardInfo($spell);
        if ($card_type['activation'] == WG_SPELL_ACTIVATION_DELAYED) {
            if ($card_type['activation_auto'] == true) {
                $instance = SpellCard::getInstanceOfCard($spell);
                $instance->castSpell($mana_card);
            } else {
                // TODO
            }
        }
    }
}
