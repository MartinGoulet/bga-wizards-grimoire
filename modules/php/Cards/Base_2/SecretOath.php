<?php

namespace WizardsGrimoire\Cards\Base_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class SecretOath extends BaseCard {

    public function isOngoingSpellActive(bool $value, int $player_id) {
        // As long as this spell has mana on it, if your opponent has a 4 power mana in their hand, they must give it to you immediately
        Globals::setIsActiveSecretOath($value, $player_id);
    }

    public static function check($cards) {
        $mana_power_4 = array_filter($cards, function ($card) {
            return ManaCard::getPower($card) == 4;
        });
        $result = array_filter($cards, function ($card) {
            return ManaCard::getPower($card) != 4;
        });
        if (sizeof($mana_power_4) > 0) {
            foreach ($mana_power_4 as $card_id => $card) {
                ManaCard::addToHand($card['id'], Globals::getIsActiveSecretOathPlayer());
            }
            Notifications::moveManaCard(Players::getOpponentId(), $mana_power_4, false);
            Notifications::secretOath(Players::getPlayerId(), $mana_power_4);
        }
        return $result;
    }
}
