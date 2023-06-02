const states = {
   client: {
      castSpellWithMana: "client_castSpellWithMana"
   },
   server: {
      castSpell: "castSpell",
      chooseNewSpell: "chooseNewSpell",
      basicAttack: "basicAttack"
   }
};

class StateManager {
   private readonly states: { [statename: string]: StateHandler };

   constructor(private game: WizardsGrimoire) {
      this.states = {
         [states.client.castSpellWithMana]: new CastSpellWithManaStates(game),

         [states.server.basicAttack]: new BasicAttackStates(game),
         [states.server.castSpell]: new CastSpellStates(game),
         [states.server.chooseNewSpell]: new ChooseNewSpellStates(game)
      };
   }

   onEnteringState(stateName: string, args: any): void {
      log("Entering state: " + stateName);

      if (this.states[stateName] !== undefined) {
         this.states[stateName].onEnteringState(args.args);
      } else {
         console.warn("State not handled", stateName);
      }
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
}
