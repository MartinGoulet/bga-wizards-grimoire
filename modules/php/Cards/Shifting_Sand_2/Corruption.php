<?php

namespace WizardsGrimoireExt\Cards\Shifting_Sand_2;

use Bga\Games\wizardsgrimoireext\Game;
use WizardsGrimoireExt\Cards\BaseCard;
use WizardsGrimoireExt\Core\ManaCard;
use WizardsGrimoireExt\Core\SpellCard;

class Corruption extends BaseCard {

    public function castSpell($args) {
        // Deal damage equal to the quantity of mana cards on 1 of your other spells. 
        // Then discard 2 mana cards off of it
        $targetPosition = intval(array_shift($args));
        $targetSpell = SpellCard::getFromRepertoire($targetPosition);

        if ($targetSpell['id'] == $this->id) {
            throw new \BgaUserException("You must target another spell.");
        }

        $position = SpellCard::getPositionInRepertoire($targetSpell);
        $manaCards = ManaCard::getCardsOnManaCoolDown($position);
        $numManaCards = count($manaCards);

        // Deal damage equal to the number of mana cards
        $this->dealDamage($numManaCards);

        // Discard up to 2 mana cards from the target spell
        $numToDiscard = min(2, $numManaCards);
        for ($i = 0; $i < $numToDiscard; $i++) {
            ManaCard::discardManaFromSpell($position);
        }

        if($numManaCards > 0)  {
            $instance = SpellCard::getInstanceOfCard($targetSpell);
            Game::get()->triggerOnAfterDiscardManaFromSpell($instance);
        }

    }
}
