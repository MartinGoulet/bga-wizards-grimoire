<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_1;

use WizardsGrimoireExt\Cards\RelicCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Notifications;
use WizardsGrimoireExt\Core\Players;
use WizardsGrimoireExt\Core\SpellCard;

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
