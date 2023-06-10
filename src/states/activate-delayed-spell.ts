class ActivateDelayedSpellStates implements StateHandler {
   private player_table: PlayerTable;

   constructor(private game: WizardsGrimoire) {}
   onEnteringState(args: ActivateDelayedSpellArgs): void {
      if (!this.game.isCurrentPlayerActive()) return;
      this.player_table = this.game.getPlayerTable(this.game.getPlayerId());

      const handleSelection = (selection: SpellCard[], lastChange: SpellCard) => {};

      this.player_table.spell_repertoire.setSelectionMode("single");
      this.player_table.spell_repertoire.onSelectionChange = handleSelection;

      const selectable_cards = this.player_table.spell_repertoire
         .getCards()
         .filter((card) => args.spells.indexOf(card.id.toString()) >= 0);

      debugger;
      this.player_table.spell_repertoire.setSelectableCards(selectable_cards);
   }
   onLeavingState(): void {}
   onUpdateActionButtons(args: ActivateDelayedSpellArgs): void {
      const handleConfirm = () => {
         alert("confirm");
      };

      this.game.addActionButton("btn_confirm", _("Confirm"), handleConfirm);
      this.game.disableButton("btn_confirm");

      if (args.spells.length == 0) {
         this.game.addActionButtonPass();
      }
   }
   restoreGameState() {}
}

interface ActivateDelayedSpellArgs {
   spells: string[];
}
