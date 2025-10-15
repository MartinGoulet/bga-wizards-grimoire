<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Players;

class Imagination extends BaseCard {

    public function castSpell($args) {
        $handCount = ManaCard::getHandCount(Players::getPlayerId());
        $opponentHandCount = ManaCard::getHandCount(Players::getOpponentId());

        if ($opponentHandCount > $handCount + 2) {
            $this->drawManaCards($opponentHandCount - $handCount);
        } else {
            $this->drawManaCards(2);
        }
    }

}