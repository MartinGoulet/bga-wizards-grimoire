<?php

namespace WizardsGrimoire\Cards\Forbidden_Scrolls;

use Bga\Games\WizardsGrimoire\Game;
use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Globals;

class TimeWalk extends BaseCard {

    public function castSpell($args) {
        $nbrDraw = intval(array_shift($args));
        if ($nbrDraw < 0 || $nbrDraw > 5) {
            throw new \BgaUserException(clienttranslate("You must draw between 0 and 5 cards."));
        }

        $this->drawManaCards($nbrDraw);

        $amount = 5 - $nbrDraw;
        if ($amount > 0) {
            $message = clienttranslate('${player_name}\'s next spell costs ${amount} less mana this turn.');
            Game::get()->notify->all('message', $message, [
                'player_name' => Game::get()->getActivePlayerName(),
                'amount' => $amount,
            ]);
        }
    
        Globals::setTimeWalkDecreaseCost($amount);
    }

    public function onAfterCastSpell() {
        $lastSpellId = Globals::getSpellPlayed();
        if ($lastSpellId != $this->id && Globals::getTimeWalkDecreaseCost() > 0) {
            Globals::setTimeWalkDecreaseCost(0);
        }
    }

}