<?php

namespace WizardsGrimoire\Core;

class Notifications {

    static function activateSpell($player_id, $card_name) {
        $msg = clienttranslate('${player_name} activate ${card_name}');
        self::message($msg, [
            'player_id' => intval($player_id),
            'player_name' => self::getPlayerName($player_id),
            'card_name' => $card_name,
            'i18n' => ['card_name'],
        ]);
    }

    static function basicAttackCard(int $player_id, $mana_card) {
        $msg = clienttranslate('${player_name} discards ${mana_values} for is basic attack');
        self::message($msg, [
            'player_id' => intval($player_id),
            "player_name" => self::getPlayerName($player_id),
            "mana_values" => [ManaCard::getPower($mana_card)],
        ]);
    }

    static function basicAttack(int $player_id, int $damage, int $life_remaining) {
        $message = clienttranslate('${player_name} receives ${damage} from a basic attack');

        self::notifyAll('onHealthChanged', $message, [
            'player_id' => intval($player_id),
            "player_name" => self::getPlayerName($player_id),
            "life_remaining" => $life_remaining,
            "damage" => $damage,
            "nbr_damage" => $damage,
        ]);
    }

    static function castSpell($player_id, $card_name, $cards_before, $cards_after) {
        $msg = clienttranslate('${player_name} casts ${card_name}');
        self::message($msg, [
            'player_id' => intval($player_id),
            'player_name' => self::getPlayerName($player_id),
            'card_name' => $card_name,
            'i18n' => ['card_name'],
        ]);

        self::moveManaCard($player_id, $cards_before);
    }

    static function chooseSpell($player_id, $card) {
        $msg = clienttranslate('${player_name} chooses ${card_name} from the spell pool');
        self::notifyAll('onChooseSpell', $msg, [
            'player_id' => intval($player_id),
            'player_name' => self::getPlayerName($player_id),
            'card' => $card,
            'card_name' => SpellCard::getName($card),
            'i18n' => ['card_name'],
        ]);
    }

    static function destroySpell($player_id, $card) {
        self::notifyAll('onDiscardSpell', '${player_name} destroys ${card_name}', [
            'player_id' => intval($player_id),
            'player_name' => self::getPlayerName(intval($player_id)),
            'card' => $card,
            'card_name' => SpellCard::getName($card),
            'i18n' => ['card_name'],
        ]);
    }

    static function discardSpell($player_id, $card) {
        self::notifyAll('onDiscardSpell', '${player_name} discards ${card_name}', [
            'player_id' => intval($player_id),
            'player_name' => self::getPlayerName(intval($player_id)),
            'card' => $card,
            'card_name' => SpellCard::getName($card),
            'i18n' => ['card_name'],
        ]);
    }

    static function discardSpellCard($card_name) {

        self::message(clienttranslate('${player_name} discards ${card_name}'), [
            'player_name' => self::getPlayerName(Players::getPlayerId()),
            'card_name' => $card_name,
            'i18n' => ['card_name'],
        ]);
    }

    static function drawManaCards($player_id, $cards, string $card_name = null) {
        $msg = clienttranslate('${player_name} draws ${mana_values}');
        $args = [
            'player_id' => intval($player_id),
            'player_name' => self::getPlayerName($player_id),
            'mana_values' => self::getPowerValues($cards),
        ];

        if ($card_name !== null) {
            $msg = clienttranslate('${player_name} draws ${mana_values} from ${card_name}');
            $args['card_name'] = $card_name;
            $args["i18n"] = ["card_name"];
        }

        $args['cards'] = array_values($cards);
        self::notify($player_id, 'onDrawManaCards', $msg, $args);

        $msg = clienttranslate('${player_name} draws ${nbr_mana_card}');
        $args = [
            'player_id' => intval($player_id),
            'player_name' => self::getPlayerName($player_id),
            'nbr_mana_card' => sizeof($cards),
        ];

        if ($card_name !== null) {
            $msg = clienttranslate('${player_name} draws ${nbr_mana_card} from ${card_name}');
            $args['card_name'] = $card_name;
            $args["i18n"] = ["card_name"];
        }

        $args['cards'] = array_values(Game::anonynizeCards($cards));
        self::notifyAll('onDrawManaCards', $msg, $args, $player_id);
    }

    static function echoSpell(int $player_id, array $spell) {
        $message = clienttranslate('${player_name} uses ${card_name} and copy the effect of ${card_name2}');
        self::message($message, [
            'player_id' => intval($player_id),
            "player_name" => self::getPlayerName($player_id),
            'card_name' => clienttranslate("Echo"),
            'card_name2' => SpellCard::getName($spell),
            'i18n' => ['card_name', 'card_name2'],
        ]);
    }

    static function hasNoManaCard(int $player_id, int $value) {
        self::message(clienttranslate('${player_name} does not have a ${value} power mana'), [
            'player_id' => intval($player_id),
            "player_name" => self::getPlayerName($player_id),
            "value" => $value,
        ]);
    }

    static function manaDeckShuffle($cards) {
        $msg = clienttranslate("The mana deck is reshuffled because it is empty");
        self::notifyAll('onManaDeckShuffle', $msg, [
            "cards" => $cards,
        ]);
    }

    static function dealFromDeckToManaCoolDown($player_id, $card, $position) {
        $args = [
            'player_id' => Players::getPlayerId(),
            'player_name' => self::getPlayerName(Players::getPlayerId()),
            'card_name' => SpellCard::getName(SpellCard::getFromRepertoire($position, $player_id)),
            'i18n' => ['card_name'],
        ];

        $message = clienttranslate('${player_name} puts a mana card on ${card_name} from the deck');
        self::message($message, $args, $player_id);
        
        $message = clienttranslate('${player_name} puts a ${mana_values} on ${card_name} from the deck');
        $args['mana_values'] = self::getPowerValues([$card]);
        self::messageTo($player_id, $message, $args);

        self::moveManaCard($player_id, [$card]);
    }

    static function discardManaCards($player_id, $cards) {
        $message = clienttranslate('${player_name} discards ${mana_values}');
        self::message($message, [
            'player_id' => intval($player_id),
            'player_name' => self::getPlayerName($player_id),
            'mana_values' => self::getPowerValues($cards),
        ]);
        self::moveManaCard($player_id, $cards, false);
    }

    static function discardManaCardFromSpell($player_id, $card, $position) {
        $message = clienttranslate('${player_name} discards ${mana_values} from ${card_name}');
        self::message($message, [
            'player_id' => intval($player_id),
            'player_name' => self::getPlayerName($player_id),
            'mana_values' => self::getPowerValues([$card]),
            'card_name' => SpellCard::getName(SpellCard::getFromRepertoire($position, $player_id)),
            'i18n' => ['card_name'],
        ]);
        self::moveManaCard($player_id, [$card], false);
    }

    static function discardManaCardForBattleVision($player_id, $card) {
        $message = clienttranslate('${player_name} discards ${mana_values} for ${card_name}');
        self::message($message, [
            'player_id' => intval($player_id),
            'player_name' => self::getPlayerName($player_id),
            'mana_values' => self::getPowerValues([$card]),
            'card_name' => clienttranslate('Battle vision'),
            'i18n' => ['card_name'],
        ]);
        self::moveManaCard($player_id, [$card], false);
    }

    static function pickUpManaCardFromSpell($player_id, $card, $position, $spell_player_id = null) {
        if ($spell_player_id == null) {
            $spell_player_id = $player_id;
        }
        $message = clienttranslate('${player_name} pick up ${mana_values} from ${card_name}');
        self::messageTo($player_id, $message, [
            'player_id' => intval($player_id),
            'player_name' => self::getPlayerName($player_id),
            'mana_values' => self::getPowerValues([$card]),
            'card_name' => SpellCard::getName(SpellCard::getFromRepertoire($position, $spell_player_id)),
            'i18n' => ['card_name'],
        ]);
    }

    static function giveManaCards($player_id, $cards) {
        $message = clienttranslate('${player_name} gives ${mana_values} to ${player_name2}');
        $other_player_id = Players::getOpponentIdOf($player_id);
        self::message($message, [
            'player_id' => $other_player_id ,
            'player_name' => self::getPlayerName($other_player_id ),
            'player_name2' => self::getPlayerName($player_id),
            'mana_values' => self::getPowerValues($cards),
        ]);
        self::moveManaCard($player_id, $cards, false);
    }

    static function fracture($player_id, $card, $position_from, $position_to) {
        $args = [
            'player_id' => intval($player_id),
            'player_name' => self::getPlayerName($player_id),
            'player_name2' => self::getPlayerName(Players::getOpponentIdOf($player_id)),
            'card_name' => SpellCard::getName(SpellCard::getFromRepertoire($position_from, $player_id)),
            'card_name2' => SpellCard::getName(SpellCard::getFromRepertoire($position_to, $player_id)),
            'i18n' => ['card_name', 'card_name2'],
        ];
        
        $message = clienttranslate('${player_name} transfers a mana card from ${card_name} to ${card_name2}');
        self::message($message, $args, $player_id);

        $message = clienttranslate('${player_name} transfers ${mana_values} from ${card_name} to ${card_name2}');
        $args['mana_values'] = self::getPowerValues([$card]);
        self::messageTo($player_id, $message, $args);

        self::moveManaCard($player_id, [$card], false);
    }

    static function moveManaCard($player_id, $cards_before, $anonimyze = true) {
        $args = [
            'player_id' => intval($player_id),
        ];

        $cards = array_values(array_map(function ($card) {
            return ManaCard::get($card['id']);
        }, $cards_before));

        $args['cards_after'] = array_values($cards);
        self::notify($player_id, 'onMoveManaCards', '', $args);

        if ($anonimyze) {
            $args['cards_after'] = array_values(Game::anonynizeCards($cards));
        }
        self::notifyAll('onMoveManaCards', '', $args, $player_id);
    }

    public static function receiveDamageFromCard(string $card_name, int $player_id, int $damage, int $life_remaining) {
        $message = clienttranslate('${player_name} receives ${damage} from ${card_name}');

        self::notifyAll('onHealthChanged', $message, [
            'player_id' => intval($player_id),
            "player_name" => self::getPlayerName($player_id),
            "life_remaining" => $life_remaining,
            "damage" => $damage,
            "nbr_damage" => $damage,
            "card_name" => $card_name,
            "i18n" => ["card_name"],
        ]);
    }

    static function refillSpell($player_id, $card) {
        $msg = clienttranslate('${card_name} is added to the spell pool');
        self::notifyAll('onRefillSpell', $msg, [
            'card' => $card,
            'card_name' => SpellCard::getName($card),
            'i18n' => ['card_name'],
        ]);
    }

    static function revealManaCard(int $player_id, array $cards) {
        $msg = clienttranslate('${player_name} reveals ${mana_values} from mana deck');
        self::message($msg, [
            'player_id' => intval($player_id),
            "player_name" => self::getPlayerName($player_id),
            "mana_values" => self::getPowerValues($cards),
        ]);
    }

    static function revealManaCardCooldown(int $player_id, $card, $spell_name) {
        $msg = clienttranslate('${player_name} reveals ${mana_values} from the mana cooldown under ${card_name}');
        self::notifyAll('onRevealManaCardCooldown', $msg, [
            'player_id' => intval($player_id),
            "player_name" => self::getPlayerName($player_id),
            "mana_values" => self::getPowerValues([$card]),
            "card" => $card,
            'card_name' => $spell_name,
            'i18n' => ['card_name'],
        ]);
    }

    static function revealManaCardHand(int $player_id, array $cards) {
        self::message(clienttranslate('${player_name} reveals ${mana_values} from his hand'), [
            'player_id' => intval($player_id),
            "player_name" => self::getPlayerName($player_id),
            "mana_values" => self::getPowerValues($cards),
        ]);
    }

    static function secretOath(int $player_id, $cards) {
        $opponent_id = Players::getOpponentIdOf($player_id);
        self::message(clienttranslate('${card_name} : ${player_name} must gives ${mana_values} to ${player_name2}'), [
            'player_id' => intval($opponent_id),
            "player_name" => self::getPlayerName($opponent_id),
            "player_name2" => self::getPlayerName($player_id),
            "mana_values" => self::getPowerValues(array_values($cards)),
            "card_name" => _("Secret oath"),
            "i18n" => ["card_name"],
        ]);
    }

    static function skipBattleVision(int $player_id) {
        self::message(clienttranslate('${player_name} cannot use ${card_name} since he does not have a card in hand'), [
            'player_id' => $player_id,
            "player_name" => self::getPlayerName($player_id),
            "card_name" => _("Battle vision"),
            "i18n" => ["card_name"],
        ]);
    }

    static function skipChooseNewSpell() {
        $player_id = Players::getPlayerId();
        self::message(clienttranslate('${player_name} skips phase ${phase_name} since all his spells are used'), [
            'player_id' => intval($player_id),
            "player_name" => self::getPlayerName($player_id),
            "phase_name" => clienttranslate("Choose a New Spell"),
            "i18n" => ["phase_name"],
        ]);
    }

    static function spellCooldownDelayed($cards) {
        self::message(clienttranslate('${phase_name} : Discard ${mana_values} from delayed activation card(s)'), [
            "mana_values" => self::getPowerValues($cards),
            "phase_name" => clienttranslate("Spell Cool Down"),
            "i18n" => ["phase_name"],
        ]);
    }

    static function spellCooldownInstant($cards) {
        self::message(clienttranslate('${phase_name} : Discard ${mana_values} from instant activation card(s)'), [
            "mana_values" => self::getPowerValues($cards),
            "phase_name" => clienttranslate("Spell Cool Down"),
            "i18n" => ["phase_name"],
        ]);
    }

    static function spellCooldownOngoing($cards) {
        self::message(clienttranslate('${phase_name} : Discard ${mana_values} from ongoing activation card(s)'), [
            "mana_values" => self::getPowerValues($cards),
            "phase_name" => clienttranslate("Spell Cool Down"),
            "i18n" => ["phase_name"],
        ]);
    }

    static function spellNoEffect() {
        self::message(clienttranslate("The spell has no effect since requirement was not met"));
    }

    /*************************
     **** GENERIC METHODS ****
     *************************/

    protected static function notifyAll($name, $msg, $args = [], $exclude_player_id = null) {
        if ($exclude_player_id != null) {
            $args['excluded_player_id'] = $exclude_player_id;
        }
        Game::get()->notifyAllPlayers($name, $msg, $args);
    }

    protected static function notify($player_id, $name, $msg, $args = []) {
        Game::get()->notifyPlayer($player_id, $name, $msg, $args);
    }

    protected static function message($msg, $args = [], $exclude_player_id = null) {
        self::notifyAll('message', $msg, $args);
    }

    protected static function messageTo($player_id, $msg, $args = []) {
        self::notify($player_id, 'message', $msg, $args);
    }

    protected static function getPlayerName($player_id) {
        return Game::get()->getPlayerNameById($player_id);
    }

    private static function getPowerValues(array $cards) {
        return array_map(function ($card) {
            return ManaCard::getPower($card);
        }, array_values($cards));
    }
}
