<?php

namespace WizardsGrimoireExt\Cards\Forbidden_Scrolls;

use Bga\Games\wizardsgrimoireext\Game;
use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\Globals;
use WizardsGrimoireExt\Core\Players;
use WizardsGrimoireExt\Core\SpellCard;

class SilencingAmulet extends BaseCard {

    public function castSpell($args) {
        $choice = intval(array_shift($args));
        $position = intval(array_shift($args));
        switch ($choice) {
            case 1:
                $new_spell_id = intval(array_shift($args));
                $this->destroyOwnSpellAndReplace($position, $new_spell_id);
                break;
            case 2:
                return $this->destroyOpponentSpellAndReplace($position);
            default:
                throw new \BgaUserException("Invalid choice for Transference destroy spell " . $choice);
        }
    }

    public function onDestroyRelic() {
    }

    private function destroyOwnSpellAndReplace(int $position, int $new_spell_id) {
        $player_id = Players::getPlayerId();

        $oldSpell = SpellCard::getFromRepertoire($position, $player_id);
        if (!$oldSpell) {
            throw new \BgaUserException("No spell found at position " . $position);
        }

        $newSpell = SpellCard::get($new_spell_id);
        SpellCard::discardAllManaCardsOnSpell($oldSpell, Players::getPlayerId());
        SpellCard::replaceSpell($oldSpell, $newSpell, "destroy");
        SpellCard::destroyRelic(SpellCard::get($this->id));
    }

    private function destroyOpponentSpellAndReplace(int $position) {
        $player_id = Players::getOpponentId();

        $oldSpell = SpellCard::getFromRepertoire($position, $player_id);

        Game::get()->globals->set("transference_spell", $oldSpell);
        Globals::setInteractionPlayer($player_id);
        Game::get()->gamestate->nextState("opponent");
        return "stop";
    }

    public function getCastSpellInteractionArgs() {
        $args = [
            "transference_spell" => Game::get()->globals->get("transference_spell"),
        ];
        return $args;
    }

    public function castSpellInteraction($args) {
        $oldSpell = $this->getCastSpellInteractionArgs()["transference_spell"];
        $new_spell_id = intval(array_shift($args));
        $newSpell = SpellCard::get($new_spell_id);

        SpellCard::discardAllManaCardsOnSpell($oldSpell, Players::getOpponentId());
        SpellCard::replaceSpell($oldSpell, $newSpell, "destroy", Players::getOpponentId());
        SpellCard::destroyRelic(SpellCard::get($this->id));
    }
}
