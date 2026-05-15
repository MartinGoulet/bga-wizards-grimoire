<?php

namespace WizardsGrimoireExt\Cards\Forbidden_Scrolls;

use Bga\Games\wizardsgrimoireext\Game;
use Exception;
use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\Players;
use WizardsGrimoireExt\Core\SpellCard;

class Quicksand extends BaseCard {

    public function castSpell($args) {
        throw new Exception("Quicksand does not have a cast effect, it only has a trigger on mana discard.");
    }

    public function onAfterDiscardManaFromSpell(int $mana_id) {
        $ownerId = $this->getOwnerId();

        $spell = SpellCard::get($this->id);
        $position = SpellCard::getPositionInRepertoire($spell);

        $mana_cooldown_count = ManaCard::countOnTopOfManaCoolDown($position, $ownerId);
        if ($mana_cooldown_count > 0) {
            return;
        }

        $opponentHand = ManaCard::getHand(Players::getOpponentIdOf($ownerId));
        $hasCardWithPower3 = false;

        foreach ($opponentHand as $mana) {
            if (ManaCard::getPower($mana) == 3) {
                $hasCardWithPower3 = true;
                break;
            }
        }

        if ($hasCardWithPower3) {
            $message = clienttranslate('${player_name} has a mana card with power 3.');
            Game::get()->notify->all('message', $message, [
                'player_id' => Players::getOpponentIdOf($ownerId),
            ]);
            $this->dealDamage(2, Players::getOpponentIdOf($ownerId));
        } else {
            $message = clienttranslate('${player_name} does not have a mana card with power 3.');
            Game::get()->notify->all('message', $message, [
                'player_id' => Players::getOpponentIdOf($ownerId),
            ]);
            $this->dealDamage(5, Players::getOpponentIdOf($ownerId));
        }
    }

}