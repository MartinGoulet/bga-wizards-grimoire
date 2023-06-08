<?php

/**
 *------
 * BGA framework: © Gregory Isabelli <gisabelli@boardgamearena.com> & Emmanuel Colin <ecolin@boardgamearena.com>
 * WizardsGrimoire implementation : © Martin Goulet <martin.goulet@live.ca>
 *
 * This code has been produced on the BGA studio platform for use on https://boardgamearena.com.
 * See http://en.doc.boardgamearena.com/Studio for more information.
 * -----
 * 
 * wizardsgrimoire.action.php
 *
 * WizardsGrimoire main action entry point
 *
 *
 * In this file, you are describing all the methods that can be called from your
 * user interface logic (javascript).
 *       
 * If you define a method "myAction" here, then you can call it from your javascript code with:
 * this.ajaxcall( "/wizardsgrimoire/wizardsgrimoire/myAction.html", ...)
 *
 */


class action_wizardsgrimoire extends APP_GameAction {
  // Constructor: please do not modify
  public function __default() {
    if (self::isArg('notifwindow')) {
      $this->view = "common_notifwindow";
      $this->viewArgs['table'] = self::getArg("table", AT_posint, true);
    } else {
      $this->view = "wizardsgrimoire_wizardsgrimoire";
      self::trace("Complete reinitialization of board game");
    }
  }

  // TODO: defines your action entry points there

  public function chooseSpell() {
    self::setAjaxMode();

    $card_id = self::getArg("id", AT_posint, true);
    $this->game->chooseSpell($card_id);

    self::ajaxResponse();
  }

  public function castSpell() {
    self::setAjaxMode();

    $card_id = self::getArg("card_id", AT_posint, true);
    $args = self::getArg("args", AT_numberlist, true);

    // Removing last ';' if exists
    if (substr($args, -1) == ';')
      $args = substr($args, 0, -1);

    if ($args == '')
      $args = array();
    else
      $args = explode(';', $args);

    $this->game->castSpell($card_id, $args);

    self::ajaxResponse();
  }

  public function basicAttack() {
    self::setAjaxMode();

    $card_id = self::getArg("id", AT_posint, true);
    $this->game->basicAttack($card_id);

    self::ajaxResponse();
  }

  public function pass() {
    self::setAjaxMode();

    $this->game->pass();

    self::ajaxResponse();
  }

  /*
    
    Example:
  	
    public function myAction()
    {
        self::setAjaxMode();     

        // Retrieve arguments
        // Note: these arguments correspond to what has been sent through the javascript "ajaxcall" method
        $arg1 = self::getArg( "myArgument1", AT_posint, true );
        $arg2 = self::getArg( "myArgument2", AT_posint, true );

        // Then, call the appropriate method in your game logic, like "playCard" or "myAction"
        $this->game->myAction( $arg1, $arg2 );

        self::ajaxResponse( );
    }
    
    */
}
