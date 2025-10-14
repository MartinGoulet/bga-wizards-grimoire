<?php

namespace WizardsGrimoireExt\Cards\KickStarter_1;

use Bga\Games\wizardsgrimoireext\Game;
use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\Players;

class FatalFlaw extends BaseCard {

    public function castSpell($args) {
        // Deal 1 damage. Reveal the top mana card on 1 of your opponents spells, dealing additional damage equal to it's power.

        if($args == null || $args == "") {
            $this->dealDamage(1);
        } else {
            $position = intval(array_shift($args));
            $card = ManaCard::getOnTopOnManaCoolDown($position, Players::getOpponentId());
            Notifications::revealManaCardCooldown(Players::getOpponentId(), $card, $this->getCardName());
            $this->dealDamage(ManaCard::getPower($card) + 1);
            Game::get()->undoSavepoint();
        }
    }
}
