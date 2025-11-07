<?php
declare(strict_types=1);

namespace Bga\Games\WizardsGrimoireExt\States;

use Bga\GameFramework\Actions\Types\IntArrayParam;
use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use Bga\Games\WizardsGrimoireExt\Game;
use BgaUserException;
use WizardsGrimoireExt\Core\Globals;
use WizardsGrimoireExt\Core\SpellCard;

class SpellSeeingStone extends GameState
{
    function __construct(protected Game $game) {
        parent::__construct($game,
            id: ST_SPELL_SEEING_STONE,
            name: 'spellSeeingStone',
            type: StateType::ACTIVE_PLAYER,

            description: clienttranslate('${actplayer} may replace Seeing Stone with a revealed spell'),
            descriptionMyTurn: clienttranslate('${you} may replace Seeing Stone with a revealed spell'),
            transitions: [
                'relic' => ST_CAST_SPELL_INTERACTION,
                'replace' => ST_CAST_SPELL_START,
            ],
        );
    }

    public function getArgs(): array
    {
        $data = [
            'cards' => array_values($this->game->deck_spells->getCardsOnTop(4, 'deck')),
        ];
        return ['_private' => ['active' => $data]];
    }

    function onEnteringState(int $activePlayerId) {

    }

    #[PossibleAction]
    public function actSelectSpellSeeingStone(#[IntArrayParam()] array $cardOrder, int $replaceSpellId)
    {
        $deck = $this->game->deck_spells;
        $topCards = $deck->getCardsOnTop(4, 'deck');
        $topCardIds = array_map(fn($card) => (int)$card['id'], $topCards);

        // Ensure all cards are present
        foreach ($topCardIds as $cardId) {
            if (!in_array($cardId, $cardOrder) && $cardId !== $replaceSpellId) {
                throw new \BgaUserException("Invalid card selection");
            }
        }

        $deck->moveCards($topCardIds, 'temp');
        foreach ($cardOrder as $cardId) {
            $deck->insertCardOnExtremePosition($cardId, 'deck', true);
        }
        if ($replaceSpellId !== 0) {
            $deck->insertCardOnExtremePosition($replaceSpellId, 'deck', true);
            // Discard Seeing Stone
            $spellId = Globals::getSpellPlayed();
            $spell = SpellCard::get($spellId);
            SpellCard::discardAllManaCardsOnSpell($spell);

            $newSpell = SpellCard::get($replaceSpellId);
            SpellCard::replaceSpell($spell, $newSpell, 'replaceSeeingStone');

            $this->game->triggerOnAddSpellToRepertoire($newSpell);
            $this->game->gamestate->nextState('replace');
        } else {
            $this->game->gamestate->nextState('relic');
        }
    }

    function zombie(int $playerId) {
        // the code to run when the player is a Zombie
    }
}
