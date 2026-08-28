<?php

// declare(strict_types=1);

// namespace Bga\Games\WizardsGrimoire\States;

// use Bga\GameFramework\Actions\Types\IntArrayParam;
// use Bga\GameFramework\StateType;
// use Bga\GameFramework\States\GameState;
// use Bga\GameFramework\States\PossibleAction;
// use Bga\Games\WizardsGrimoire\Game;
// use WizardsGrimoire\Core\Globals;
// use WizardsGrimoire\Core\SpellCard;

// class DestroySpellReplace extends GameState {
//     function __construct(protected Game $game) {
//         parent::__construct(
//             $game,
//             id: ST_DESTROY_SPELL_REPLACE,
//             name: 'castSpellInteraction',
//             type: StateType::ACTIVE_PLAYER,
//             transitions: [
//                 'end' => ST_CAST_SPELL_START,
//             ],
//         );
//     }

//     public function getArgs(): array {
//         $args = $this->game->getArgsBase();
//         $args["spell"] = SpellCard::get(Globals::getSpellPlayed());
//         $args["previous_spell_played"] = Globals::getPreviousSpellPlayed();

//         $instance = SpellCard::getInstanceOfCard($args["spell"]);
//         if (method_exists($instance, 'getCastSpellInteractionArgs')) {
//             $interactionArgs = $instance->getCastSpellInteractionArgs();
//             $args = array_merge($args, $interactionArgs);
//         }
//         return $args;
//     }

//     #[PossibleAction]
//     public function actCastSpellInteraction(int $cardId, int $activePlayerId, array $args): string {
//         $this->game->actCastSpellInteraction($args);
//     }

//     function zombie(int $playerId) {
//         // the code to run when the player is a Zombie
//     }
// }
