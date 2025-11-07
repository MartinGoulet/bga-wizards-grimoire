<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use Bga\Games\wizardsgrimoireext\Game;
use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Players;

class Imagination extends BaseCard {

    public function castSpell($args) {
        $choice = intval(array_shift($args));
        if ($choice === 1) {
            $this->drawManaCards(2);
        } else {
            $this->drawUntilEqualOpponent();
        }
    }

    private function drawUntilEqualOpponent() {
        $handCount = ManaCard::getHandCount(Players::getPlayerId());
        $opponentHandCount = ManaCard::getHandCount(Players::getOpponentId());

        if ($opponentHandCount <= $handCount) {
            $message = clienttranslate(
                '${player_name} has as many or more mana cards than their opponent. No mana cards drawn.'
            );
            Game::get()->notify->all("message", $message, [
                'player_id' => Players::getPlayerId(),
            ]);
        } else {
            $this->drawManaCards($opponentHandCount - $handCount);
        }
    }
}
