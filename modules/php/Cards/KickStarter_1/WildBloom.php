<?php

namespace WizardsGrimoire\Cards\KickStarter_1;

use BgaSystemException;
use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\SpellCard;

class WildBloom extends BaseCard {

    public function castSpell($args) {
        // When you discard the last mana off this spell, you may immediately cast an instant spell of yours that has 0 mana on it for no cost

        $spell_pos = intval(array_shift($args));
        $spell = SpellCard::getFromRepertoire($spell_pos);

        if(ManaCard::countOnTopOfManaCoolDown($spell_pos) > 0) {
            throw new BgaSystemException("Card has mana cooldown");
        }

        if(SpellCard::getCardInfo($spell)['activation'] !== WG_SPELL_ACTIVATION_INSTANT) {
            throw new BgaSystemException("Card is not an instant");
        }

        Globals::setSpellPlayed($spell['id']);
        Game::get()->activateInstantSpell($spell, $args);
    }

    public function isDelayedSpellTrigger() {
        $card = $this->getCard();
        $position = intval($card['location_arg']);
        $count = ManaCard::countOnTopOfManaCoolDown($position);
        return $count == 0;
    }
}
