<?php

namespace WizardsGrimoireExt\Cards\Forbidden_Scrolls;

use Bga\Games\wizardsgrimoireext\Game;
use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Players;

class Quicksand extends BaseCard {

    public function castSpell($args) {

    }

    public function onAfterDiscardManaFromSpell() {
        $opponentHand = ManaCard::getHand(Players::getOpponentId());
        $hasCardWithPower3 = false;

        foreach ($opponentHand as $mana) {
            if (ManaCard::getPower($mana) == 3) {
                $hasCardWithPower3 = true;
                break;
            }
        }

        if ($hasCardWithPower3) {
            $message = clienttranslate('${player_name} has a mana card with power 3.');
            Game::get()->notify->all('message', $message, [
                'player_id' => Players::getOpponentId(),
            ]);
            $this->dealDamage(2);
        } else {
            $message = clienttranslate('${player_name} does not have a mana card with power 3.');
            Game::get()->notify->all('message', $message, [
                'player_id' => Players::getOpponentId(),
            ]);
            $this->dealDamage(5);
        }
    }

}