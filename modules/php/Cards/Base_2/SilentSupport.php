<?php

namespace WizardsGrimoire\Cards\Base_2;

use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;

class SilentSupport extends BaseCard {

    public function castSpell($args) {
        // Each time you discard off this spell, pick up a mana off 1 of your other spells

        $position = intval(array_shift($args));
        $player_card = ManaCard::hasUnderSpell($position);
        $player_card_after = ManaCard::drawFromManaCoolDown($position);
        Notifications::moveManaCard(Players::getPlayerId(), [$player_card], [$player_card_after]);
    }
}
