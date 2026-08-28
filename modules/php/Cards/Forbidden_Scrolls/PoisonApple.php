<?php

namespace WizardsGrimoire\Cards\Forbidden_Scrolls;

use WizardsGrimoire\Cards\RelicCard;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;

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