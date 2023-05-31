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

/*
 * State constants
 */
define('ST_BGA_GAME_SETUP', 1);
define('ST_END_GAME', 99);

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