<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_1;

use WizardsGrimoire\Cards\RelicCard;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;

class CrystalShard extends RelicCard {

    public function castSpell($args) {
        $mana = ManaCard::createCrystalShard();
        $spell = SpellCard::get($this->id);

        Notifications::crystalShard(Players::getPlayerId(), $spell, $mana);
    }
    
    public function onDestroyRelic()
    {
        $spell = SpellCard::get($this->id);
        SpellCard::destroyRelic($spell, 'crystal');
    }
}
