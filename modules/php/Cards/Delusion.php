<?php

namespace WizardsGrimoire\Cards;

use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Objects\CardLocation;

class Delusion extends BaseCard {

    public function castSpell($args) {
        // Deal 3 damage. You may pick up a mana card off 1 of your opponent's spells

        $this->dealDamage(3);

        if(sizeof($args) == 1) {
            $player_id = Player::getPlayerId();
            $opponent_id = Players::getOpponentId();
            $opponent_stack_pos = intval(array_shift($args));
            $card = Game::get()->$deck_manas->getCardOnTop(CardLocation::PlayerManaCoolDown($opponent_id, $opponent_stack_pos));
            if($card !== null) {
                $card_after = Game::get()->deck_manas->pickCard(CardLocation::PlayerManaCoolDown($opponent_id, $opponent_stack_pos), $player_id);
                Notifications::moveManaCard($player_id, [$card], [$card_after]);
            }
        }
    }

}
