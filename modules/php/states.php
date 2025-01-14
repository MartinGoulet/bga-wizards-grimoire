<?php

namespace WizardsGrimoire\Core;

use WizardsGrimoire\Core\Game;
use WizardsGrimoire\Core\Notifications;

trait StateTrait {

    //////////////////////////////////////////////////////////////////////////////
    //////////// Game state actions
    ////////////

    /*
        Here, you can create methods defined as "game state actions" (see "action" property in states.inc.php).
        The action method of state X is called everytime the current game state is set to X.
    */

    function stNewTurn() {
        $player_id = intval($this->getActivePlayerId());
        Players::setPlayerId($player_id);
        Globals::setPlayerTurn($player_id);

        $this->incStat(1, WG_STAT_TURN_NUMBER);
        $this->incStat(1, WG_STAT_TURN_NUMBER, $player_id);

        Globals::resetOnNewTurn();

        Events::onCheckOngoingActiveSpell();
        Game::undoSavepoint();

        $next_state = ManaCard::getHandCount() > 10 ? "discard" : "spell";
        $this->gamestate->nextState($next_state);
    }

    function stPreChooseNewSpell() {
        for ($i = 1; $i <= 6; $i++) {
            if (ManaCard::countOnTopOfManaCoolDown($i) == 0) {
                $this->gamestate->nextState("next");
                return;
            }
        }
        Notifications::skipChooseNewSpell();
        $this->gamestate->nextState("end");
    }

    function stSpellCoolDownInstantDelayed() {

        $this->stSpellCoolDownInstant();
        $this->stSpellCoolDownDelayed();
        $this->stSpellCoolDownRememberOngoing();

        $cooldownIds = Globals::getCoolDownDelayedSpellIds();

        if (Players::getPlayerLife(Players::getPlayerId()) <= 0 || Players::getPlayerLife(Players::getOpponentId()) <= 0) {
            $this->gamestate->nextState('dead');
        } else if (is_array($cooldownIds) && sizeof($cooldownIds) > 0) {
            $this->gamestate->nextState('delayed');
        } else {
            $this->gamestate->nextState('next');
        }
    }

    function stSpellCoolDownInstant() {

        $cards = [];

        for ($i = 1; $i <= 6; $i++) {
            $mana_card = ManaCard::getOnTopOnManaCoolDown($i);
            if ($mana_card) {
                $spell = SpellCard::getFromRepertoire($i);
                $spell_info = SpellCard::getCardInfo($spell);

                if ($spell_info['activation'] == WG_SPELL_ACTIVATION_INSTANT) {
                    $cards[] = $mana_card;
                    ManaCard::addOnTopOfDiscard($mana_card['id']);
                }
            }
        }

        if (sizeof($cards) > 0) {
            Notifications::spellCooldownInstant($cards);
            $player_id = intval($this->getActivePlayerId());
            Notifications::moveManaCard($player_id, $cards, false);
        }
    }

    function stSpellCoolDownDelayed() {

        $cards = [];
        $spell_delayed = [];

        for ($i = 1; $i <= 6; $i++) {
            $mana_card = ManaCard::getOnTopOnManaCoolDown($i);
            if ($mana_card) {
                $spell = SpellCard::getFromRepertoire($i);
                $spell_info = SpellCard::getCardInfo($spell);

                if ($spell_info['activation'] == WG_SPELL_ACTIVATION_DELAYED) {
                    $cards[] = $mana_card;
                    ManaCard::addOnTopOfDiscard($mana_card['id']);
                    $instance = SpellCard::getInstanceOfCard($spell);

                    if ($spell_info['activation_auto'] == true) {
                        $instance->castSpell($mana_card);
                    } else {
                        if ($instance->isDelayedSpellTrigger()) {
                            $spell_delayed[] = $spell['id'];
                        }
                    }
                }
            }
        }

        if (sizeof($cards) > 0) {
            Notifications::spellCooldownDelayed($cards);
            $player_id = intval($this->getActivePlayerId());
            Notifications::moveManaCard($player_id, $cards, false);
        }

        if (sizeof($spell_delayed) > 0) {
            Globals::setCoolDownDelayedSpellIds($spell_delayed);
        }
    }

    function stSpellCoolDownRememberOngoing() {
        $positions = [];

        for ($i = 1; $i <= 6; $i++) {
            $mana_card = ManaCard::getOnTopOnManaCoolDown($i);
            if ($mana_card) {
                $spell = SpellCard::getFromRepertoire($i);
                $spell_info = SpellCard::getCardInfo($spell);

                if ($spell_info['activation'] == WG_SPELL_ACTIVATION_ONGOING) {
                    $positions[] = $i;
                }
            }
        }

        Globals::setCoolDownOngoingSpellIds($positions);
    }

    function stSpellCoolDownOngoing() {

        $cards = [];
        $positions = Globals::getCoolDownOngoingSpellIds();
        if (!is_array($positions)) {
            $positions = [1, 2, 3, 4, 5, 6];
        }

        foreach ($positions as $i) {
            $mana_card = ManaCard::getOnTopOnManaCoolDown($i);
            if ($mana_card) {
                $spell = SpellCard::getFromRepertoire($i);
                $spell_info = SpellCard::getCardInfo($spell);

                if ($spell_info['activation'] == WG_SPELL_ACTIVATION_ONGOING) {
                    if (ManaCard::countOnTopOfManaCoolDown($i) == 1) {
                        $instance = SpellCard::getInstanceOfCard($spell);
                        $instance->isOngoingSpellActive(false, 0);
                    }
                    $cards[] = $mana_card;
                    ManaCard::addOnTopOfDiscard($mana_card['id']);
                }
            }
        }

        if (sizeof($cards) > 0) {
            Notifications::spellCooldownOngoing($cards);
            $player_id = intval($this->getActivePlayerId());
            Notifications::moveManaCard($player_id, $cards, false);
        }

        $this->gamestate->nextState();
    }

    function stGainMana() {
        ManaCard::draw(3);
        $this->gamestate->nextState();
    }

    function stPreCastSpell() {
        if (Players::getPlayerLife(Players::getPlayerId()) <= 0 || Players::getPlayerLife(Players::getOpponentId()) <= 0) {
            $this->gamestate->nextState('dead');
        } else {
            $this->giveExtraTime(Players::getPlayerId());
            Events::onCheckOngoingActiveSpell();
            $this->gamestate->nextState('cast');
        }
    }

    function stSwitchToOpponent() {
        $this->giveExtraTime(Globals::getInteractionPlayer());
        Game::get()->gamestate->changeActivePlayer(Globals::getInteractionPlayer());
        Game::get()->gamestate->nextState();
    }

    function stReturnToCurrentPlayerDelayedSpell() {
        if (Globals::getInteractionPlayer() != Players::getPlayerId()) {
            Game::get()->gamestate->changeActivePlayer(Players::getPlayerId());
            Game::undoSavepoint();
        }
        Globals::setInteractionPlayer(0);
        $next_state = sizeof(Globals::getCoolDownDelayedSpellIds()) > 0 ? "delayed" : "end";
        Game::get()->gamestate->nextState($next_state);
    }

    function stReturnToCurrentPlayer() {
        if (Globals::getInteractionPlayer() != Players::getPlayerId()) {
            Game::get()->gamestate->changeActivePlayer(Players::getPlayerId());
            Game::undoSavepoint();
        }
        Globals::setInteractionPlayer(0);
        Game::get()->gamestate->nextState();
    }

    function stSwithPlayer() {
        Game::undoSavepoint();
        $opponent_id = Players::getOpponentId();
        Players::setPlayerId($opponent_id);
        $this->giveExtraTime($opponent_id);

        Game::get()->gamestate->changeActivePlayer($opponent_id);
        Game::get()->gamestate->nextState();
    }

    function stPreBasicAttack() {
        $this->giveExtraTime(Players::getPlayerId());
        Globals::setCurrentBasicAttackPower(0);
        if (ManaCard::getHandCount() == 0) {
            Globals::setPreviousBasicAttackPower(0);
            Globals::setLastBasicAttackDamage(0);
            $this->gamestate->nextState("end");
        } else {
            $this->gamestate->nextState("next");
        }
    }

    function stBasicAttackDamage() {
        $opponent_id = Players::getOpponentId();
        $damage = Globals::getCurrentBasicAttackPower();

        $life_remaining = Players::dealDamage($damage, $opponent_id);
        Notifications::basicAttack($opponent_id, $damage, $life_remaining);
        Stats::damageWithBasicAttack($damage, $opponent_id);

        Globals::setPreviousBasicAttackPower($damage);
        Globals::setLastBasicAttackDamage($damage);
        Globals::setCurrentBasicAttackPower(0);

        if (Players::getPlayerLife(Players::getOpponentId()) <= 0) {
            $this->gamestate->nextState('dead');
        } else {
            $this->gamestate->nextState('attack');
        }
    }

    function stBasicAttackEnd() {
        $card = ManaCard::getBasicAttack();

        if (Globals::getIsActivePowerHungry()) {
            ManaCard::addToHand($card['id'], Globals::getIsActivePowerHungryPlayer());
            Notifications::moveManaCard(Players::getPlayerId(), [$card], false);
            Game::undoSavepoint();
        } else {
            ManaCard::addOnTopOfDiscard($card['id']);
            Notifications::moveManaCard(Players::getPlayerId(), [$card], false);
        }


        $this->gamestate->nextState();
    }

    function stNextPlayer() {
        Events::onCheckOngoingActiveSpell();

        $cards = SpellCard::getOngoingActiveSpells(Players::getPlayerId());
        foreach ($cards as $card_id => $card) {
            $instance = SpellCard::getInstanceOfCard($card);
            if (method_exists($instance, 'onTurnEnd')) {
                $instance->onTurnEnd();
            }
        }

        if (Players::getPlayerLife(Players::getPlayerId()) <= 0 || Players::getPlayerLife(Players::getOpponentId()) <= 0) {
            $this->gamestate->nextState('dead');
        } else {
            $this->giveExtraTime(Players::getPlayerId());

            $player_id = intval($this->getActivePlayerId());
            if ($player_id == Players::getPlayerId()) {
                $this->activeNextPlayer();
            }

            $this->gamestate->nextState('next');
        }
    }

    public function stPreEndOfGame() {

        $first_player = Players::getFirstPlayer();
        $second_player = Players::getOpponentIdOf($first_player);

        $game = Game::get();

        if (Players::getPlayerLife($first_player) > Players::getPlayerLife($second_player)) {
            $game->initStat('player', WG_STAT_WIN_FIRST_CHOICE, 100, $first_player);
            $game->initStat('player', WG_STAT_WIN_FIRST_ATTACKER, 0, $second_player);
        } else {
            $game->initStat('player', WG_STAT_WIN_FIRST_CHOICE, 0, $first_player);
            $game->initStat('player', WG_STAT_WIN_FIRST_ATTACKER, 100, $second_player);
        }

        $this->gamestate->nextState();
    }
}
