<?php

declare(strict_types=1);

namespace Bga\Games\WizardsGrimoire\States;

use Bga\GameFramework\StateType;
use Bga\GameFramework\States\GameState;
use Bga\Games\WizardsGrimoire\Game;
use WizardsGrimoire\Cards\Forbidden_Scrolls\SilencingAmulet;
use WizardsGrimoire\Cards\KickStarter_1\WildBloom;
use WizardsGrimoire\Core\Globals;
use WizardsGrimoire\Core\ManaCard;
use WizardsGrimoire\Core\Notifications;
use WizardsGrimoire\Core\Players;
use WizardsGrimoire\Core\SpellCard;

class DestroySpell extends GameState
{
    function __construct(protected Game $game)
    {
        parent::__construct(
            $game,
            id: ST_DESTROY_SPELL,
            type: StateType::GAME,
            transitions: [
                "opponent" => ST_DESTROY_ACTIVATE_DELAYED,
                "delayed" => ST_DESTROY_ACTIVATE_DELAYED,
                "destroy" => ST_DESTROY_SPELL,  // self-loop pour le mana suivant
                "continue" => ST_CAST_SPELL_START,
                "castSpellInteraction" => ST_CAST_SPELL_INTERACTION,
            ],
        );
    }

    function onEnteringState(int $activePlayerId)
    {

        Globals::setCoolDownDelayedSpellIds([]);  // safety reset, should be already empty

        $queue = $this->globals->get("destroy_mana_queue", []);
        $callback = $this->globals->get("destroy_callback");
        $interactionPlayer = $this->globals->get("interaction_player");


        if (empty($queue)) {
            $spell = $this->globals->get("destroy_target_spell");

            if ($callback === "own") {
                // $newSpell = SpellCard::get($this->globals->get("destroy_new_spell_id"));
                // SpellCard::replaceSpell($spell, $newSpell, "destroy");

                // $spellId = $this->globals->get("transference_spell_id");
                // $spellPlayed = SpellCard::get($spellId);
                // if (SpellCard::getInstanceOfCard($spellPlayed) instanceof SilencingAmulet) {
                //     SpellCard::destroyRelic(SpellCard::get($spellId));
                // }

                // $this->gamestate->nextState("continue");

                $spellId = intval($this->globals->get("transference_spell_id"));
                Globals::setSpellPlayed($spellId);
                Globals::setInteractionPlayer(0);
                $this->gamestate->nextState("castSpellInteraction");
            } else {
                // L'opponent doit choisir son nouveau spell
                Players::setPlayerId(Players::getOpponentIdOf($interactionPlayer));
                Globals::setInteractionPlayer(0);
                // $spellPlayedId = Globals::getSpellPlayed();
                // $spellPlayed = SpellCard::get($spellPlayedId);
                // var_dump([
                //     "interactionPlayer" => $interactionPlayer,
                //     "currentPlayerId" => Players::getPlayerId(),
                //     "spell" => array_merge(
                //         $spell,
                //         SpellCard::getCardInfo($spell)
                //     ),
                //     "spellPlayed" => array_merge(
                //         $spellPlayed,
                //         SpellCard::getCardInfo($spellPlayed)
                //     )
                // ]);
                // die("OK");
                Globals::setSpellPlayed(intval($this->globals->get("transference_spell_id")));
                $this->gamestate->changeActivePlayer(Players::getOpponentId());
                $this->gamestate->nextState("castSpellInteraction");
            }
            return;
        }

        // Pop un mana et le discard
        $mana_id = array_shift($queue);
        $this->globals->set("destroy_mana_queue", $queue);

        $spell = $this->globals->get("destroy_target_spell");
        $instance = SpellCard::getInstanceOfCard($spell);
        $cardInfo = SpellCard::getCardInfo($spell);
        $playerId = $callback === "own" ? Players::getPlayerId() : Players::getOpponentId();

        $mana_before = ManaCard::get($mana_id);
        $cards_before = [$mana_before];
        ManaCard::addOnTopOfDiscard($mana_id);
        Notifications::discardManaCards($playerId, $cards_before);

        Game::get()->triggerOnAfterDiscardManaFromSpell($instance, $mana_id);
        // + Notifications si besoin

        if ($instance instanceof WildBloom) {
            if (!empty($queue)) {
                $this->gamestate->nextState("destroy");          // self-loop → prochain mana
                return;
            } else {
                Globals::setCoolDownDelayedSpellIds([$spell['id']]);
                $this->gamestate->nextState("delayed");   // → ST_DESTROY_ACTIVATE_DELAYED
                return;
            }
        }

        if ($cardInfo["activation"] == WG_SPELL_ACTIVATION_DELAYED && $instance->isDelayedSpellTrigger() && !method_exists($instance, "onAfterDiscardManaFromSpell")) {
            // Si c'est un spell à activation différée qui a un trigger mais pas de méthode onDelayedSpellTrigger, on considère que le trigger doit être activé maintenant
            $instance->castSpell(ManaCard::get($mana_id));
            $this->gamestate->nextState("destroy");          // self-loop → prochain mana
            return;
        }

        if (method_exists($instance, "onAfterDiscardManaFromSpell")) {
            $this->gamestate->nextState("destroy");          // self-loop → prochain mana
            return;
        }

        // Vérifier si un trigger a demandé une interaction
        if (Globals::getInteractionPlayer() > 0) {
            Globals::setCoolDownDelayedSpellIds([$spell['id']]);
            Players::setPlayerId(Globals::getInteractionPlayer());
            Globals::setInteractionPlayer(0);
            $this->gamestate->changeActivePlayer(Players::getPlayerId());
            $this->gamestate->nextState("opponent");  // → switch + ST_DESTROY_ACTIVATE_DELAYED
        } elseif ($cardInfo["activation"] == WG_SPELL_ACTIVATION_DELAYED) {
            Globals::setCoolDownDelayedSpellIds([$spell['id']]);
            $this->gamestate->nextState("delayed");   // → ST_DESTROY_ACTIVATE_DELAYED
        } else {
            $this->gamestate->nextState("destroy");          // self-loop → prochain mana
        }
    }
}
