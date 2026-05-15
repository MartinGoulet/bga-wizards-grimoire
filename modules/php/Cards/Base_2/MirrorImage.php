<?php

namespace WizardsGrimoireExt\Cards\Base_2;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;

class MirrorImage extends BaseCard {

    public function castSpell($args) {
        // Gain mana equal to the quantity of mana cards on 1 of your spells
        $position = intval(array_shift($args));
        $mana_cooldown_count = ManaCard::countOnTopOfManaCoolDown($position);
        if ($mana_cooldown_count > 0) {
            $this->drawManaCards($mana_cooldown_count);
        }
    }
}
