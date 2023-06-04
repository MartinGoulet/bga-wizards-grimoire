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

   restoreGameState() {}
}
