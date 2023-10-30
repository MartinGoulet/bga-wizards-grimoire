<?php

namespace WizardsGrimoire\Cards\Sand_1;

use BgaUserException;
use WizardsGrimoire\Cards\BaseCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\SpellCard;

class Transfigure extends BaseCard {

    public function castSpell($args) {
        // Gain 4 mana. You may choose to gain 1 less mana to destroy 1 of your spells that has no mana on it
        if ($args == null || $args == "") {
            $this->drawManaCards(4);
        } else {
            $this->drawManaCards(3);

            $old_spell_pos = intval(array_shift($args));
            $new_spell_id = intval(array_shift($args));

            $new_spell = SpellCard::get($new_spell_id);
            $old_spell = SpellCard::getFromRepertoire($old_spell_pos);

            if(ManaCard::countOnTopOfManaCoolDown($old_spell_pos) > 0) {
                throw new BgaUserException("The spell must have no mana on it");
            }

            SpellCard::replaceSpell($old_spell, $new_spell, "destroy");
        }
    }
}
