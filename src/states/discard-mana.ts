class DiscardManaStates implements StateHandler {
   private player_table: PlayerTable;
   private nbr_cards_to_discard: number;

   constructor(private game: WizardsGrimoire) {}

   onEnteringState(args: any): void {
      if (!this.game.isCurrentPlayerActive()) return;

      this.player_table = this.game.getPlayerTable(this.game.getPlayerId());
      this.nbr_cards_to_discard = this.player_table.hand.getCards().length - 10;

      const handleChange = () => {
         const nbr_cards_selected = this.player_table.hand.getSelection().length;
         this.game.toggleButtonEnable("btn_confirm", nbr_cards_selected == this.nbr_cards_to_discard);
      };

      this.player_table.hand.setSelectionMode("multiple");
      this.player_table.hand.onSelectionChange = handleChange;
   }

   onLeavingState(): void {
      if (!this.game.isCurrentPlayerActive()) return;

      this.player_table.hand.setSelectionMode("none");
      this.player_table.hand.onSelectionChange = null;
      this.nbr_cards_to_discard = 0;
   }

   onUpdateActionButtons(args: SelectManaDeckArgs): void {
      const handleConfirm = () => {
         const selected_card_ids = this.player_table.hand.getSelection().map((x) => x.id);
         if (selected_card_ids.length == this.nbr_cards_to_discard) {
            this.game.takeAction("discardMana", {
               args: selected_card_ids.join(";"),
            });
         }
      };

      this.game.addActionButtonDisabled("btn_confirm", _("Confirm"), handleConfirm);
   }

   restoreGameState() {}
}
