<?php

namespace WizardsGrimoire\Cards\Shifting_Sand_2;

use Bga\Games\WizardsGrimoire\Game;
use WizardsGrimoire\Cards\RelicCard;
use WizardsGrimoire\Core\SpellCard;

class SeeingStone extends RelicCard {

    public function castSpell($args) {
        Game::get()->undoSavepoint();
        Game::get()->gamestate->nextState('seeing_stone');
        return 'stop';
    }

    public function onDestroyRelic()
    {
    }

    public function castSpellInteraction($args) {
        SpellCard::destroyRelic(SpellCard::get($this->id));
    }

}