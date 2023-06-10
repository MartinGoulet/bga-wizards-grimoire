class ChooseNewSpellStates implements StateHandler {
   private player_table: PlayerTable;

   constructor(private game: WizardsGrimoire) {}

   onEnteringState(args: any): void {
      if (!this.game.isCurrentPlayerActive()) return;

      this.player_table = this.game.getPlayerTable(this.game.getPlayerId());

      const available_slots = this.player_table.getSpellSlotAvailables();

      if (available_slots.length == 0) {
         this.game.tableCenter.spellPool.setSelectionMode("none");
         return;
      }

      const handleSelection = (selection: SpellCard[], lastChange: SpellCard) => {
         if (selection && selection.length === 1) {
            this.game.enableButton("btn_confirm", "blue");
         } else {
            this.game.disableButton("btn_confirm");
         }
      };

      this.game.tableCenter.spellPool.setSelectionMode("single");
      this.game.tableCenter.spellPool.onSelectionChange = handleSelection;
   }
   onLeavingState(): void {
      this.game.tableCenter.spellPool.setSelectionMode("none");
      this.game.tableCenter.spellPool.onSelectionChange = null;
   }
   onUpdateActionButtons(args: any): void {
      this.player_table = this.game.getPlayerTable(this.game.getPlayerId());
      const available_slots = this.player_table.getSpellSlotAvailables();

      if (available_slots.length > 0) {
         this.game.addActionButtonDisabled("btn_confirm", _("Choose"), () => {
            const selectedSpell = this.game.tableCenter.spellPool.getSelection()[0];
            this.game.takeAction("chooseSpell", { id: selectedSpell.id });
         });
      }
      if (this.player_table.spell_repertoire.getCards().length == 6) {
         this.game.addActionButtonPass();
      }
   }

   restoreGameState() {}
}
