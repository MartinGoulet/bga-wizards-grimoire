<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_2;

use Bga\Games\WizardsGrimoire\Game;
use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Players;

class MindGames extends BaseCard {

    public function castSpell($args) {
        $hand = ManaCard::getHand(Players::getOpponentId());

        if(empty($hand)) {
            $this->dealDamage(0);
            return;
        }
        
        // Get the lowest power mana cards
        $minPower = min(array_map(function ($card) {
            return ManaCard::getPower($card);
        }, $hand));

        Game::get()->undoSavepoint();
        
        $this->dealDamage($minPower);
    }

}