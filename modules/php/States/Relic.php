<?php
declare(strict_types=1);

namespace Bga\Games\WizardsGrimoireExt\States;

use Bga\GameFramework\Actions\Types\JsonParam;
use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\GameFramework\States\PossibleAction;
use Bga\Games\WizardsGrimoireExt\Game;
use WizardsGrimoireExt\Core\Globals;
use WizardsGrimoireExt\Core\Players;
use WizardsGrimoireExt\Core\SpellCard;

class Relic extends GameState
{
    function __construct(protected Game $game) {
        parent::__construct($game,
            id: ST_RELIC,
            name: 'relic',
            type: StateType::ACTIVE_PLAYER,

            description: clienttranslate('${actplayer} must choose a new spell to replace the relic'),
            descriptionMyTurn: clienttranslate('${you} must choose a new spell to replace the relic'),
        );
    }

    public function getArgs(): array
    {
        
        $args = $this->game->getArgsBase();
        $args["spell"] = SpellCard::get(Globals::getSpellPlayed());
        $args["previous_spell_played"] = Globals::getPreviousSpellPlayed();

        $card_type = SpellCard::getCardInfo($args["spell"]);
        $args['_no_notify'] = 
            !isset($card_type["is_relic"]) || $card_type["is_relic"] !== true;
        return $args;
    }

    function onEnteringState(array $args)
    {
        if($args['_no_notify'] === true) {
            return CastSpellEnd::class;
        }

    }

    #[PossibleAction]
    public function actCastSpellInteraction(#[JsonParam(associative: false, alphanum: false)] object $args) {
        if (is_array($args->values)) {
            $args = $args->values;
        } else {
            $args = $args->values ? [$args->values] : [];
        }

        $new_spell_id = intval(array_shift($args));
        $new_card = SpellCard::get($new_spell_id);
        SpellCard::addNewSpell($new_card);

        return CastSpellEnd::class;
    }

    function zombie(int $playerId) {
        // the code to run when the player is a Zombie
    }
}
