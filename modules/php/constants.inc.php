<?php

/*
 * Game options 
 */
define('WG_OPTION_NO', 1);
define('WG_OPTION_YES', 2);

define('WG_GAME_OPTION_DIFFICULTY_ID', 100);
define('WG_GAME_OPTION_DIFFICULTY', 'gameOptionDifficulty');
define('WG_DIFFICULTY_BEGINNER', 1);
define('WG_DIFFICULTY_ADVANCED', 2);

define('WG_GAME_OPTION_EXT_KICKSTARTER_1_ID', 101);
define('WG_GAME_OPTION_EXT_KICKSTARTER_1', 'gameOptionKickStarter1');


/*
 * Game variables
 */
define('WG_VAR_SLOT_COUNT', 'slot_count');
define('WG_VAR_CURRENT_PLAYER', 'current_player');
define('WG_VAR_PLAYER_TURN', 'player_turn');
define('WG_VAR_INTERACTION_PLAYER', 'interaction_player');
define('WG_VAR_SPELL_PLAYED', 'spell_played');
define('WG_VAR_SKIP_INTERACTION', 'skip_interaction');
define('WG_VAR_DISCOUNT_NEXT_SPELL', 'discount_next_spell');
define('WG_VAR_DISCOUNT_ATTACK_SPELL', 'discount_attack_spell');
define('WG_VAR_AMNESIA', 'amnesia');
define('WG_VAR_IS_ACTIVE_BATTLE_VISION', 'is_active_battle_vision');
define('WG_VAR_IS_ACTIVE_GROWTH', 'is_active_growth');
define('WG_VAR_IS_ACTIVE_LULLABY', 'is_active_lullaby');
define('WG_VAR_IS_ACTIVE_POWER_HUNGRY', 'is_active_power_hungry');
define('WG_VAR_IS_ACTIVE_PUPPETMASTER', 'is_active_puppetmaster');
define('WG_VAR_IS_ACTIVE_SECRET_OATH', 'is_active_secret_oath');
define('WG_VAR_CONSECUTIVELY_ATTACK_SPELL_CAST', 'cons_attk_spell');
define('WG_VAR_PREVIOUS_BASIC_ATTACK_POWER', 'prev_basic_attack');
define('WG_VAR_CURRENT_BASIC_ATTACK_POWER', 'curr_basic_attack');
define('WG_VAR_PREVIOUS_SPELL_PLAYED', 'prev_spell_played');
define('WG_VAR_PREVIOUS_SPELL_DAMAGE', 'prev_spell_damage');
define('WG_VAR_UNDO_AVAILABLE', 'undo_available');
define('WG_VAR_STATS_ACTIVATED', 'stats_activated');
define('WG_VAR_FIRST_PLAYER', 'first_player');

define('WG_VAR_LAST_BASIC_ATTACK_DAMAGE', 'last_basic_dmg');

define('WG_GV_COOLDOWN_DELAYED_SPELLS', 'cooldown_delayed_spells');
define('WG_GV_COOLDOWN_ONGOING_SPELLS', 'cooldown_ongoing_spells');

/*
 * State constants
 */
define('ST_BGA_GAME_SETUP', 1);
define('ST_PRE_END_OF_GAME', 98);
define('ST_END_GAME', 99);

define('ST_PLAYER_NEW_TURN', 10);
define('ST_CHOOSE_NEW_SPELL_START', 15);
define('ST_CHOOSE_NEW_SPELL', 2);
define('ST_DISCARD_MANA', 11);


define('ST_SPELL_COOL_DOWN', 3);
define('ST_SPELL_COOL_DOWN_ONGOING', 36);

define('ST_SPELL_CD_ACTIVATE_DELAYED', 31);
define('ST_SPELL_CD_CAST_SPELL_INTERACTION', 32);
define('ST_SPELL_CD_CAST_SPELL_SWITCH_OPPONENT', 33);
define('ST_SPELL_CD_CAST_SPELL_RETURN_CURRENT_PLAYER', 34);

define('ST_GAIN_MANA', 4);

define('ST_CAST_SPELL_START', 49);
define('ST_CAST_SPELL', 5);
define('ST_CAST_SPELL_INTERACTION', 51);
define('ST_CAST_SPELL_SWITCH_OPPONENT', 52);
define('ST_CAST_SPELL_RETURN_CURRENT_PLAYER', 53);

define('ST_CAST_SPELL_CD_ACTIVATE_DELAYED', 54);
define('ST_CAST_SPELL_CD_INTERACTION', 55);
define('ST_CAST_SPELL_CD_SWITCH_OPPONENT', 56);
define('ST_CAST_SPELL_CD_RETURN_CURRENT_PLAYER', 57);

define('ST_CAST_SPELL_SWITCH_TO_OPPONENT', 58);
define('ST_CAST_SPELL_SWITCH_TO_CURRENT_PLAYER', 59);

define('ST_CAST_SPELL_CD_OPPONENT_ACTIVATE_DELAYED', 154);
define('ST_CAST_SPELL_CD_OPPONENT_INTERACTION', 155);
define('ST_CAST_SPELL_CD_OPPONENT_SWITCH_OPPONENT', 156);
define('ST_CAST_SPELL_CD_OPPONENT_RETURN_CURRENT_PLAYER', 157);


define('ST_BASIC_ATTACK_START', 66);
define('ST_BASIC_ATTACK', 6);
define('ST_BASIC_ATTACK_DAMAGE', 61);
define('ST_BASIC_ATTACK_BATTLE_VISION', 62);
define('ST_BASIC_ATTACK_SWITCH_OPPONENT', 63);
define('ST_BASIC_ATTACK_RETURN_CURRENT_PLAYER', 64);
define('ST_BASIC_ATTACK_END', 65);

define('ST_NEXT_PLAYER', 7);


/*
 * Statistic variables 
 */
define('WG_STAT_TURN_NUMBER', 'turns_number');
define('WG_STAT_WIN_FIRST_CHOICE', 'win_first_spell_choice');
define('WG_STAT_WIN_FIRST_ATTACKER', 'win_first_attacker');

define('WG_STAT_NBR_DRAFT', 'nbr_draft');
define('WG_STAT_NBR_DRAFT_ATTACK', 'nbr_draft_red');
define('WG_STAT_NBR_DRAFT_UTILITY', 'nbr_draft_purple');
define('WG_STAT_NBR_DRAFT_REGENERATION', 'nbr_draft_green');

define('WG_STAT_NBR_DRAFT_COST_1', 'nbr_draft_cost_1');
define('WG_STAT_NBR_DRAFT_COST_2', 'nbr_draft_cost_2');
define('WG_STAT_NBR_DRAFT_COST_3', 'nbr_draft_cost_3');
define('WG_STAT_NBR_DRAFT_COST_4', 'nbr_draft_cost_4');
define('WG_STAT_NBR_DRAFT_COST_5', 'nbr_draft_cost_5');

define('WG_STAT_NBR_MANA_DRAW', 'nbr_mana_draw');

define('WG_STAT_DMG_WHEN_2_SPELLS', 'nbr_dmg_spells_2');
define('WG_STAT_DMG_WHEN_3_SPELLS', 'nbr_dmg_spells_3');
define('WG_STAT_DMG_WHEN_4_SPELLS', 'nbr_dmg_spells_4');
define('WG_STAT_DMG_WHEN_5_SPELLS', 'nbr_dmg_spells_5');
define('WG_STAT_DMG_WHEN_6_SPELLS', 'nbr_dmg_spells_6');
define('WG_STAT_DMG_BASIC_ATTACK',  'nbr_dmg_basic_attack');

/**
 * Spell Activation
 */
define('WG_SPELL_ACTIVATION_INSTANT', 'instant');
define('WG_SPELL_ACTIVATION_DELAYED', 'delayed');
define('WG_SPELL_ACTIVATION_ONGOING', 'ongoing');

/**
 * Spell Type
 */
define('WG_SPELL_TYPE_ATTACK', 'red');
define('WG_SPELL_TYPE_REGENERATION', 'green');
define('WG_SPELL_TYPE_UTILITY', 'purple');

/**
 * Spell Icon
 */
define('WG_ICON_SET_BASE_1', 'Base_1');
define('WG_ICON_SET_BASE_2', 'Base_2');
define('WG_ICON_SET_KICKSTARTER_1', 'KickStarter_1');
