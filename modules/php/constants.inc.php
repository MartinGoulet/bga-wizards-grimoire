<?php

/*
 * Game options 
 */
define('WG_GAME_OPTION_DIFFICULTY_ID', 100);
define('WG_GAME_OPTION_DIFFICULTY', 'gameOptionDifficulty');
define('WG_DIFFICULTY_BEGINNER', 1);
define('WG_DIFFICULTY_NORMAL', 2);
define('WG_DIFFICULTY_ADVANCED', 3);

/*
 * Game variables
 */
define('WG_VAR_SLOT_COUNT', 'slot_count');

define('WG_GV_COOLDOWN_DELAYED_SPELLS', 'cooldown_delayed_spells');

/*
 * State constants
 */
define('ST_BGA_GAME_SETUP', 1);
define('ST_END_GAME', 99);

define('ST_PLAYER_NEW_TURN', 10);
define('ST_CHOOSE_NEW_SPELL', 2);

define('ST_SPELL_COOL_DOWN', 3);
define('ST_SPELL_CD_ACTIVATE_DELAYED', 31);

define('ST_GAIN_MANA', 4);

define('ST_CAST_SPELL', 5);

define('ST_BASIC_ATTACK', 6);

define('ST_NEXT_PLAYER', 7);


/*
 * Statistic variables 
 */
define('WG_STAT_TURN_NUMBER', 'turns_number');

/**
 * Spell Activation
 */
define('WG_SPELL_ACTIVATION_INSTANT', 'instant');
define('WG_SPELL_ACTIVATION_DELAYED', 'delayed');
define('WG_SPELL_ACTIVATION_ONGOING', 'ongoing');

/**
 * Spell Type
 */
define('WG_SPELL_TYPE_DAMAGE', 'red');
define('WG_SPELL_TYPE_REGENERATION', 'green');
define('WG_SPELL_TYPE_UTILITY', 'purple');

/**
 * Spell Icon
 */
define('WG_ICON_BEGINNER', '+');
define('WG_ICON_INTERMEDIATE', '++');
define('WG_ICON_ADVANCE', 'scroll');