<?php

namespace WizardsGrimoire\Cards\Base_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Events;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class Delusion extends BaseCard {

    public function castSpell($args) {
        // Deal 3 damage. You may pick up a mana card off 1 of your opponent's spells

        $this->dealDamage(3);

        if (sizeof($args) == 1) {
            $player_id = Players::getPlayerId();
            $opponent_id = Players::getOpponentId();
            $opponent_stack_pos = intval(array_shift($args));
            $card = ManaCard::getOnTopOnManaCoolDown($opponent_stack_pos, $opponent_id);
            if ($card !== null) {
                ManaCard::addToHand($card['id']);
                Notifications::moveManaCard($player_id, [$card]);
                Events::onManaPickedUpUnderSpell($opponent_stack_pos, $opponent_id);
            }
        }
    }
}
