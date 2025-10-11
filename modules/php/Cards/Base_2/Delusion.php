<?php

namespace WizardsGrimoireExt\Cards\Base_2;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\Events;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\Players;

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
                Notifications::pickUpManaCardFromSpell($player_id, $card, $opponent_stack_pos, $opponent_id);
                Notifications::moveManaCard($player_id, [$card]);
                Events::onManaPickedUpUnderSpell($opponent_stack_pos, $opponent_id);
            }
        }
    }
}
