const states = {
   client: {
      castSpellWithMana: "client_castSpellWithMana",
      question: "client_question",
      selectMana: "client_selectMana",
      selectManaDeck: "client_selectManaDeck",
      selectManaDiscard: "client_selectManaDiscard",
      selectManaHand: "client_selectManaHand",
   },
   server: {
      castSpell: "castSpell",
      chooseNewSpell: "chooseNewSpell",
      basicAttack: "basicAttack",
      activateDelayedSpell: "activateDelayedSpell",
   },
};

class StateManager {
   private readonly states: { [statename: string]: StateHandler };
   private readonly client_states: StateHandler[] = [];

   constructor(private game: WizardsGrimoire) {
      this.states = {
         [states.client.castSpellWithMana]: new CastSpellWithManaStates(game),
         [states.client.question]: new QuestionStates(game),
         [states.client.selectMana]: new SelectManaStates(game),
         [states.client.selectManaDeck]: new SelectManaDeckStates(game),
         [states.client.selectManaDiscard]: new SelectManaDiscardStates(game),
         [states.client.selectManaHand]: new SelectManaHandStates(game),

         [states.server.activateDelayedSpell]: new ActivateDelayedSpellStates(game),
         [states.server.basicAttack]: new BasicAttackStates(game),
         [states.server.castSpell]: new CastSpellStates(game),
         [states.server.chooseNewSpell]: new ChooseNewSpellStates(game),
      };
   }

   onEnteringState(stateName: string, args: any): void {
      log("Entering state: " + stateName);

      if (this.states[stateName] !== undefined) {
         this.states[stateName].onEnteringState(args.args);
         if (stateName.startsWith("client_")) {
            this.client_states.push(this.states[stateName]);
         } else {
            this.client_states.splice(0);
         }
      } else {
         this.client_states.splice(0);
         console.warn("State not handled", stateName);
      }
      console.log("client states", this.client_states);
   }

   onLeavingState(stateName: string): void {
      log("Leaving state: " + stateName);

      if (this.states[stateName] !== undefined) {
         this.states[stateName].onLeavingState();
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

   restoreGameState() {
      while (this.client_states.length > 0) {
         const state = this.client_states.pop();
         state.restoreGameState();
      }
   }
}
