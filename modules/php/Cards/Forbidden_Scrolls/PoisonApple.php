<?php

namespace WizardsGrimoireExt\Cards\Forbidden_Scrolls;

use WizardsGrimoireExt\Cards\RelicCard;
use WizardsGrimoireExt\Core\Players;
use WizardsGrimoireExt\Core\SpellCard;

class PoisonApple extends RelicCard {

    public function castSpell($args) {
        // Deal 2 damage to yourself. Gain 3 mana
        $this->dealDamage(2, Players::getPlayerId());
        $this->drawManaCards(3);
    }
    
    public function onDestroyRelic()
    {
        SpellCard::destroyRelic(SpellCard::get($this->id));
    }

}