<?php

declare(strict_types=1);

namespace Bga\Games\WizardsGrimoire\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\Games\WizardsGrimoire\Game;
use WizardsGrimoire\Core\SpellCard;

class CastSpellEnd extends GameState {
    function __construct(protected Game $game) {
        parent::__construct(
            $game,
            id: ST_CAST_SPELL_END,
            type: StateType::GAME,

            transitions: [
                '' => ST_CAST_SPELL_START,
            ],
            updateGameProgression: false,
        );
    }

    public function getArgs(): array {
        return ['_no_notify' => true];
    }

    function onEnteringState(int $activePlayerId) {

        $spells = SpellCard::getCardsFromRepertoire();
        $discard = SpellCard::getCardsFromDiscardPile();
        $spells = array_merge($spells, $discard);
        
        foreach ($spells as $spell_card) {
            $card_instance = SpellCard::getInstanceOfCard($spell_card);
            if (method_exists($card_instance, 'onAfterCastSpell')) {
                $card_instance->onAfterCastSpell();
            }
        }

        $this->gamestate->nextState('');
    }
}
