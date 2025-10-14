<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Players;

class IceBlast extends BaseCard {

    public function castSpell($args) {
        // Choose 1: Discard your hand and deal 5 damage, or place a mana card from the mana deck on 1 of your opponent's spells
        $player_id = Players::getPlayerId();

        if(sizeof($args) == 0) {
            $this->dealDamage(5);
            Players::discardHand($player_id);
        } else {
            $position = intval(array_shift($args));
            ManaCard::dealFromDeckToManaCoolDown($position, Players::getOpponentId());
        }
    }

}