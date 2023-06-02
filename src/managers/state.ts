class StateManager {
   private readonly states: { [statename: string]: StateHandler };

   constructor(private game: WizardsGrimoire) {
      this.states = {
         chooseNewSpell: new ChooseNewSpellStates(game),
         castSpell: new CastSpellStates(game)
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

class ChooseNewSpellStates implements StateHandler {
   constructor(private game: WizardsGrimoire) {}
   onEnteringState(args: any): void {
      if (!this.game.isCurrentPlayerActive()) return;

      const spellPool = this.game.tableCenter.spellPool;

      spellPool.setSelectionMode("single");
      spellPool.onSelectionChange = (selection: SpellCard[], lastChange: SpellCard) => {
         if (selection && selection.length === 1) {
            this.game.enableButton("btn_confirm", "blue");
         } else {
            this.game.disableButton("btn_confirm");
         }
      };
   }
   onLeavingState(): void {
      this.game.tableCenter.spellPool.setSelectionMode("none");
      this.game.tableCenter.spellPool.onSelectionChange = null;
   }
   onUpdateActionButtons(args: any): void {
      this.game.addActionButtonDisabled("btn_confirm", _("Confirm"), () => {
         const selectedSpell = this.game.tableCenter.spellPool.getSelection()[0];
         this.game.takeAction("chooseSpell", { id: selectedSpell.id });
      });
   }
}

class CastSpellStates implements StateHandler {
   constructor(private game: WizardsGrimoire) {}

   onEnteringState(args: any): void {
      if (!this.game.isCurrentPlayerActive()) return;
      const player_table = this.game.getPlayerTable(this.game.getPlayerId());
      const repertoire = player_table.spell_repertoire;

      repertoire.setSelectionMode("single");
      repertoire.onSelectionChange = (selection: SpellCard[], lastChange: SpellCard) => {
         if (selection && selection.length === 1 && player_table.canCast(selection[0])) {
            this.game.enableButton("btn_cast", "blue");
         } else {
            this.game.disableButton("btn_cast");
         }
      };
   }

   onLeavingState(): void {
      const repertoire = this.game.getPlayerTable(this.game.getPlayerId()).spell_repertoire;
      repertoire.setSelectionMode("none");
      repertoire.onSelectionChange = null;
   }

   onUpdateActionButtons(args: any): void {
      this.game.addActionButtonDisabled("btn_cast", _("Cast spell"), () => {
         const selectedSpell = this.game.tableCenter.spellPool.getSelection()[0];
         this.game.takeAction("chooseSpell", { id: selectedSpell.id });
      });
      this.game.addActionButtonRed("btn_pass", _("Pass"), () => {
         this.game.takeAction("pass");
      });
   }
}
