<?php

namespace WizardsGrimoireExt\Cards;

use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\SpellCard;

abstract class RelicCard extends BaseCard {

    public function castSpellInteraction($args) {
        $new_spell_id = intval(array_shift($args));
        $new_spell = SpellCard::get($new_spell_id);
        SpellCard::addNewSpell($new_spell);
    }
}
