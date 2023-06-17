class SelectManaStates implements StateHandler {
   private player_table: PlayerTable;

   constructor(private game: WizardsGrimoire) { }

   onEnteringState(args: SelectManaDeckArgs): void {
      if (!this.game.isCurrentPlayerActive()) return;

      const { exclude, player_id, count } = args;
      this.player_table = this.game.getPlayerTable(player_id);

      const handleChange = (selection: ManaCard[], lastChange: ManaCard) => {
         let nbr_cards_selected = this.player_table
            .getManaDecks(exclude)
            .filter((x) => x.getSelection().length > 0).length;

         // Single selection
         if (args.exact && args.count == 1 && nbr_cards_selected > 1) {
            this.player_table.getManaDecks(exclude).forEach((deck) => {
               if (!deck.contains(lastChange)) {
                  deck.unselectAll();
               }
            });
            nbr_cards_selected = 1;
         }

         if (args.exact) {
            this.game.toggleButtonEnable("btn_confirm", nbr_cards_selected == count);
         } else {
            this.game.toggleButtonEnable("btn_confirm", nbr_cards_selected <= count);
         }
      };

      this.player_table.getManaDecks(exclude).forEach((deck) => {
         deck.setSelectionMode("single");
         deck.onSelectionChange = handleChange;
      });
   }

   onLeavingState(): void {
      this.player_table.getManaDecks().forEach((deck) => {
         deck.setSelectionMode("none");
         deck.onSelectionChange = null;
      });
   }

   onUpdateActionButtons(args: SelectManaDeckArgs): void {
      const handleConfirm = () => {
         const selected_cards = this.player_table
            .getManaDecks(args.exclude)
            .filter((x) => x.getSelection().length > 0)
            .map((x) => x.getSelection()[0].id);

         if (selected_cards.length < args.count) {
            if (!args.exact) {
               const text = _("Are-you sure to not take all mana card?");
               this.game.confirmationDialog(text, () => {
                  this.game.actionManager.addArgument(selected_cards.join(","));
                  this.game.actionManager.activateNextAction();
               });
            }
         } else {
            this.game.actionManager.addArgument(selected_cards.join(","));
            this.game.actionManager.activateNextAction();
         }
      };
      this.game.addActionButton("btn_confirm", _("Confirm"), handleConfirm);
      this.game.addActionButtonClientCancel();
   }

   restoreGameState() {
      this.player_table.getManaDecks().forEach((deck) => deck.unselectAll());
   }
}

interface SelectManaDeckArgs {
   player_id: number;
   card: SpellCard;
   count: number;
   exact: boolean;
   exclude: number[];
}
