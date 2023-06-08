class SelectManaHandStates implements StateHandler {
   private player_table: PlayerTable;

   constructor(private game: WizardsGrimoire) {}

   onEnteringState(args: SelectManaArgs): void {
      if (!this.game.isCurrentPlayerActive()) return;

      const { card, player_id, count } = args;
      this.player_table = this.game.getPlayerTable(player_id);

      const handleChange = () => {
         const nbr_cards_selected = this.player_table.hand.getSelection().length;
         if (args.exact) {
            this.game.toggleButtonEnable("btn_confirm", nbr_cards_selected == count);
         } else {
            this.game.toggleButtonEnable("btn_confirm", nbr_cards_selected <= count);
         }
      };

      this.player_table.hand.setSelectionMode("single");
      this.player_table.hand.onSelectionChange = handleChange;
   }

   onLeavingState(): void {
      this.player_table.hand.setSelectionMode("none");
      this.player_table.hand.onSelectionChange = null;
   }

   onUpdateActionButtons(args: SelectManaArgs): void {
      const handleConfirm = () => {
         const selected_card_ids = this.player_table.hand.getSelection().map((x) => x.id);

         if (selected_card_ids.length < args.count) {
            if (!args.exact) {
               const text = _("Are-you sure to not take all mana card?");
               this.game.confirmationDialog(text, () => {
                  this.game.actionManager.addArgument(selected_card_ids.join(","));
                  this.game.actionManager.activateNextAction();
               });
            }
         } else {
            this.game.actionManager.addArgument(selected_card_ids.join(","));
            this.game.actionManager.activateNextAction();
         }
      };
      this.game.addActionButton("btn_confirm", _("Confirm"), handleConfirm);
      this.game.addActionButtonClientCancel();
   }

   restoreGameState() {}
}

interface SelectManaArgs {
   player_id: number;
   card: SpellCard;
   count: number;
   exact: boolean;
}
