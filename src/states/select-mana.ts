class SelectManaStates implements StateHandler {
   private player_table: PlayerTable;

   constructor(private game: WizardsGrimoire) {}

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

      this.player_table.getManaDecks().forEach((deck, index) => {
         if (exclude.indexOf(index + 1) < 0) {
            deck.setSelectionMode("single");
            deck.onSelectionChange = handleChange;
         } else {
            deck
               .getCards()
               .forEach((card) => deck.getCardElement(card).classList.add("bga-cards_disabled-card"));
         }
      });
   }

   onLeavingState(): void {
      this.player_table.getManaDecks().forEach((deck) => {
         deck.setSelectionMode("none");
         deck.onSelectionChange = null;
      });
      document
         .querySelectorAll(".bga-cards_disabled-card")
         .forEach((value) => value.classList.remove("bga-cards_disabled-card"));
   }

   onUpdateActionButtons(args: SelectManaDeckArgs): void {
      const handleConfirm = () => {
         const selected_cards = this.player_table
            .getManaDecks(args.exclude)
            .filter((x) => x.getSelection().length > 0)
            .map((x) => x.getSelection()[0].id);

         if (selected_cards.length < args.count) {
            if (!args.exact) {
               const text = _("Are you sure that is how many mana cards you would like to select?");
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
      return new Promise<boolean>((resolve) => {
         this.player_table.getManaDecks().forEach((deck) => deck.unselectAll());
         resolve(true);
      });
   }
}

interface SelectManaDeckArgs {
   player_id: number;
   card: SpellCard;
   count: number;
   exact: boolean;
   exclude: number[];
}
