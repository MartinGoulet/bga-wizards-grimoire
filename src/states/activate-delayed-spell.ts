class ActivateDelayedSpellStates implements StateHandler {
   private player_table: PlayerTable;

   constructor(private game: WizardsGrimoire) {}
   onEnteringState(args: ActivateDelayedSpellArgs): void {
      if (!this.game.isCurrentPlayerActive()) return;
      this.player_table = this.game.getCurrentPlayerTable();

      const handleSelection = (selection: SpellCard[], lastChange: SpellCard) => {
         this.game.toggleButtonEnable("btn_confirm", selection.length == 1);
      };

      this.player_table.spell_repertoire.setSelectionMode("single");
      this.player_table.spell_repertoire.onSelectionChange = handleSelection;

      const selectable_cards = this.player_table.spell_repertoire
         .getCards()
         .filter((card) => args.spells && args.spells.indexOf(card.id.toString()) >= 0);

      this.player_table.spell_repertoire.setSelectableCards(selectable_cards);
   }
   onLeavingState(): void {
      if (this.player_table) {
         this.player_table.spell_repertoire.setSelectionMode("none");
         this.player_table.spell_repertoire.onSelectionChange = null;
         this.player_table = null;
      }
   }
   onUpdateActionButtons(args: ActivateDelayedSpellArgs): void {
      const handleConfirm = () => {
         const selection = this.player_table.spell_repertoire.getSelection()[0];
         this.game.actionManager.setup("activateDelayedSpell");
         this.game.actionManager.addActionDelayed(selection);
         this.game.actionManager.activateNextAction();
      };

      this.game.addActionButton("btn_confirm", _("Confirm"), handleConfirm);
      this.game.disableButton("btn_confirm");

      if (args.spells.length == 0) {
         this.game.addActionButtonPass();
      }
   }
   restoreGameState() {
      return new Promise<boolean>((resolve) => resolve(true));
   }
}

interface ActivateDelayedSpellArgs {
   spells: string[];
}
