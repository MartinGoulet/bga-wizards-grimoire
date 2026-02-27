<?php

namespace WizardsGrimoireExt\Cards\Base_2;

use WizardsGrimoireExt\Cards\OngoingBaseCard;
use WizardsGrimoireExt\Core\Globals;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\Players;
use WizardsGrimoireExt\Core\SpellCard;

class SecretOath extends OngoingBaseCard {

    public function isActive(): bool {
        return false; // card banned
        // $isActive = $this->isActiveAtLeastOneMana();

        // if($isActive) {
        //     $opponent_id = Players::getOpponentIdOf($this->getOwnerId());
        //     $cards = ManaCard::getHand($opponent_id);
        //     $this->internalCheck($cards, $opponent_id);
        // }

        // return $isActive;
    }

    public function getArguments(): array {
        return [
            'name' => 'secretoath',
            'active' => false, // $this->isActive(),
        ];
    }

    public static function check() {
        // /** @var SecretOath $card */
        // $card = SpellCard::getInstanceOfCardFromClass(SecretOath::class);
        
        // if ($card !== null) {
        //     $card->isActive();
        // }

        // card banned
    }
    
    // private function internalCheck($cards, $opponent_id) {
    //     $mana_power_4 = array_filter($cards, function ($card) {
    //         return ManaCard::getPower($card) == 4;
    //     });
    //     if (sizeof($mana_power_4) > 0) {
    //         foreach ($mana_power_4 as $card_id => $card) {
    //             ManaCard::addToHand($card['id'], Globals::getIsActiveSecretOathPlayer());
    //         }
    //         Notifications::moveManaCard($opponent_id, $mana_power_4, false);
    //         Notifications::secretOath(Globals::getIsActiveSecretOathPlayer(), $mana_power_4);
    //     }
    // }
}
