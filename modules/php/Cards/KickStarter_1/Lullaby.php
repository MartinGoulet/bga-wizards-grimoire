<?php

namespace WizardsGrimoire\Cards\KickStarter_1;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\ManaCard;

class Lullaby extends BaseCard {

    public function isOngoingSpellActive(bool $value, int $player_id) {
        // As long as there is mana on this spell, if you have 0 mana cards in your hand, gain 2 mana cards
        Globals::setIsActiveLullaby($value, $player_id);
    }

    public static function check() {
        if (Globals::getIsActiveLullaby()) {
            $count = ManaCard::getHandCount(Globals::getIsActiveLullabyPlayer());
            if ($count == 0) {
                ManaCard::draw(2, Globals::getIsActiveLullabyPlayer());
            }
        }
    }
}
