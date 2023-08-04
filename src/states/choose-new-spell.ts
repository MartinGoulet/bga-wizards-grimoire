class ChooseNewSpellStates implements StateHandler {
   private player_table: PlayerTable;

   constructor(private game: WizardsGrimoire) {}

   onEnteringState(args: any): void {
      if (!this.game.isCurrentPlayerActive()) return;

      this.player_table = this.game.getCurrentPlayerTable();

      if (this.player_table.getSpellSlotAvailables().length == 0) {
         this.clearSelectionMode();
      } else if (this.player_table.spell_repertoire.getCards().length < 6) {
         this.onEnteringStateChoose();
         this.game.setGamestateDescription();
      } else {
         const available_slots = this.player_table.getSpellSlotAvailables();
         if (available_slots.length > 0) {
            this.game.actionManager.setup("replaceSpell", "actionCastSpell_Replace");
            this.game.actionManager.activateNextAction();
         }
      }
   }

   onEnteringStateChoose(): void {
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
      this.clearSelectionMode();
   }
   onUpdateActionButtons(args: any): void {
      this.player_table = this.game.getCurrentPlayerTable();

      const handleConfirm = () => {
         const selectedSpell = this.game.tableCenter.spellPool.getSelection()[0];
         if (selectedSpell != null) {
            this.game.takeAction("chooseSpell", { id: selectedSpell.id });
         }
      };

      const handleReplace = () => {
         this.game.actionManager.setup("replaceSpell", "actionCastSpell_Replace");
         this.game.actionManager.activateNextAction();
      };

      if (this.player_table.spell_repertoire.getCards().length < 6) {
         this.game.addActionButtonDisabled("btn_confirm", _("Choose"), handleConfirm);
      } else {
         const available_slots = this.player_table.getSpellSlotAvailables();
         if (available_slots.length > 0) {
            this.game.addActionButton("btn_replace", _("Replace"), handleReplace);
         }
      }
      if (this.player_table.spell_repertoire.getCards().length == 6) {
         this.game.addActionButtonPass();
      }
   }

   restoreGameState() {
      return new Promise<boolean>((resolve) => resolve(true));
   }

   clearSelectionMode(): void {
      this.game.tableCenter.spellPool.setSelectionMode("none");
      this.game.tableCenter.spellPool.onSelectionChange = null;
   }
}
