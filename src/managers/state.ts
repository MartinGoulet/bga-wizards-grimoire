const states = {
   client: {
      arcaneTactics: "client_arcaneTactics",
      badFortune: "client_badFortune",
      castSpellWithMana: "client_castSpellWithMana",
      eclipse: "client_eclipse",
      question: "client_question",
      replaceSpell: "client_replaceSpell",
      selectMana: "client_selectMana",
      selectManaDeck: "client_selectManaDeck",
      selectManaDiscard: "client_selectManaDiscard",
      selectManaHand: "client_selectManaHand",
      selectManaReturnDeck: "client_selectManaReturnDeck",
      selectSpell: "client_selectSpell",
      selectSpellPool: "client_selectSpellPool",
   },
   server: {
      discardMana: "discardMana",
      castSpell: "castSpell",
      castSpellInteraction: "castSpellInteraction",
      chooseNewSpell: "chooseNewSpell",
      basicAttack: "basicAttack",
      basicAttackBattleVision: "basicAttackBattleVision",
      activateDelayedSpell: "activateDelayedSpell",
      playerNewTurn: "playerNewTurn",
   },
};

class StateManager {
   private readonly states: { [statename: string]: StateHandler };
   private readonly client_states: StateHandler[] = [];

   constructor(private game: WizardsGrimoire) {
      this.states = {
         [states.client.badFortune]: new BadFortuneStates(game),
         [states.client.castSpellWithMana]: new CastSpellWithManaStates(game),
         [states.client.eclipse]: new EclipseStates(game),
         [states.client.question]: new QuestionStates(game),
         [states.client.replaceSpell]: new ReplaceSpellStates(game),
         [states.client.selectMana]: new SelectManaStates(game),
         [states.client.selectManaDeck]: new SelectManaDeckStates(game),
         [states.client.selectManaDiscard]: new SelectManaDiscardStates(game),
         [states.client.selectManaHand]: new SelectManaHandStates(game),
         [states.client.selectManaReturnDeck]: new SelectManaReturnDeckStates(game),
         [states.client.selectSpell]: new SelectSpellStates(game),
         [states.client.selectSpellPool]: new SelectSpellPoolStates(game),

         [states.server.activateDelayedSpell]: new ActivateDelayedSpellStates(game),
         [states.server.discardMana]: new DiscardManaStates(game),
         [states.server.basicAttack]: new BasicAttackStates(game),
         [states.server.basicAttackBattleVision]: new BasicAttackBattleVisionStates(game),
         [states.server.castSpell]: new CastSpellStates(game),
         [states.server.castSpellInteraction]: new CastSpellInteractionStates(game),
         [states.server.chooseNewSpell]: new ChooseNewSpellStates(game),
         [states.server.playerNewTurn]: new PlayerNewTurnStates(game),
      };
   }

   onEnteringState(stateName: string, args: any): void {
      log("Entering state: " + stateName);

      if (args.phase) {
         this.game.gameOptions.setPhase(Number(args.phase));
      } else {
         this.game.gameOptions.setPhase(99);
      }

      if (args.args?.ongoing_spells) {
         args.args.ongoing_spells.forEach((value) => {
            if (value.active) log(value);
            this.game.toggleOngoingSpell(value);
         });
         Object.keys(args.args.players).forEach((player_id) => {
            const value = Number(args.args.players[player_id]);
            this.game.getPlayerPanel(Number(player_id)).turn_counter.setValue(value);
         });
      }

      if (this.states[stateName] !== undefined) {
         this.states[stateName].onEnteringState(args.args);
         if (stateName.startsWith("client_")) {
            this.client_states.push(this.states[stateName]);
         } else {
            this.client_states.splice(0);
         }
      } else {
         this.client_states.splice(0);
         if (isDebug) {
            console.warn("State not handled", stateName);
         }
      }
      console.log("client states", this.client_states);
   }

   onLeavingState(stateName: string): void {
      log("Leaving state: " + stateName);

      if (this.states[stateName] !== undefined) {
         if (this.game.isCurrentPlayerActive()) {
            this.states[stateName].onLeavingState();
         }
      }
   }

   onUpdateActionButtons(stateName: string, args: any): void {
      log("onUpdateActionButtons: " + stateName);
      if (this.states[stateName] !== undefined) {
         if (this.game.isCurrentPlayerActive()) {
            this.states[stateName].onUpdateActionButtons(args);
         }
      }
   }

   async restoreGameState() {
      return new Promise(async (resolve) => {
         while (this.client_states.length > 0) {
            const state = this.client_states.pop();
            await state.restoreGameState();
         }
         resolve(true);
      });
   }
}
