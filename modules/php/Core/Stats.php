<?php

namespace WizardsGrimoire\Core;

use BgaSystemException;

/*
 * Stats manager : allows to easily access stats ...
 */

class Stats {

    static function chooseSpell($player_id, $card) {
        if (!self::isStatActive()) return;

        $spell_info = SpellCard::getCardInfo($card);
        $stats = [
            WG_SPELL_TYPE_ATTACK => WG_STAT_NBR_DRAFT_ATTACK,
            WG_SPELL_TYPE_REGENERATION => WG_STAT_NBR_DRAFT_REGENERATION,
            WG_SPELL_TYPE_UTILITY => WG_STAT_NBR_DRAFT_UTILITY,
        ];

        Game::get()->incStat(1, WG_STAT_NBR_DRAFT, $player_id);
        Game::get()->incStat(1, $stats[$spell_info['type']], $player_id);

        if (!self::isStatActive(2)) return;

        $cost = intval($spell_info['cost']);

        $stats = [
            1 => WG_STAT_NBR_DRAFT_COST_1,
            2 => WG_STAT_NBR_DRAFT_COST_2,
            3 => WG_STAT_NBR_DRAFT_COST_3,
            4 => WG_STAT_NBR_DRAFT_COST_4,
            5 => WG_STAT_NBR_DRAFT_COST_5,
        ];

        Game::get()->incStat(1, $stats[$cost], $player_id);
    }

    static function damageWithSpell($damage, $player_received_damage, $spell) {
        if (!self::isStatActive()) return;

        $player_id = Players::getOpponentIdOf($player_received_damage);
        $spells = SpellCard::getCardsFromRepertoire($player_id);
        $spells_count = sizeof($spells);

        $stats = [
            2 => WG_STAT_DMG_WHEN_2_SPELLS,
            3 => WG_STAT_DMG_WHEN_3_SPELLS,
            4 => WG_STAT_DMG_WHEN_4_SPELLS,
            5 => WG_STAT_DMG_WHEN_5_SPELLS,
            6 => WG_STAT_DMG_WHEN_6_SPELLS,
        ];

        Game::get()->incStat($damage, $stats[$spells_count], $player_id);
    }

    static function damageWithBasicAttack($damage, $player_received_damage) {
        if (!self::isStatActive()) return;

        $player_id = Players::getOpponentIdOf($player_received_damage);
        Game::get()->incStat($damage, WG_STAT_DMG_BASIC_ATTACK, $player_id);
    }

    static function replaceSpell($player_id, $newSpell) {
        if (!self::isStatActive()) return;

        self::chooseSpell($player_id, $newSpell);
    }

    private static function isStatActive(int $version = 1) {
        return intval(Game::get()->getGameStateValue(WG_VAR_STATS_ACTIVATED)) >= $version;
    }
}
