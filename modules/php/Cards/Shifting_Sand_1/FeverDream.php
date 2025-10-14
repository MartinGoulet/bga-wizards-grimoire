<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use WizardsGrimoireExt\Cards\BaseCard;

class FeverDream extends BaseCard {

    public function onAddSpellToRepertoire(array $card) {
        $this->dealDamage(2);
    }

}