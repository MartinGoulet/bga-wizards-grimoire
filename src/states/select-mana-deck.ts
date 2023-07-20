class SelectManaDeckStates implements StateHandler {
   private player_table: PlayerTable;

   constructor(private game: WizardsGrimoire) {}

   onEnteringState(args: SelectManaDeckArgs): void {
      if (!this.game.isCurrentPlayerActive()) return;

      const { exclude, player_id, count } = args;
      this.player_table = this.game.getPlayerTable(player_id);

      const decks = this.player_table.getManaDeckWithSpellOver(exclude);

      const handleChange = (lastDeck: ManaDeck) => {
         let nbr_decks_selected = decks.filter((x) => x.isDeckSelected).length;
         // Single selection
         if (args.exact && args.count == 1 && nbr_decks_selected > 1) {
            decks.forEach((deck) => {
               if (deck !== lastDeck) {
                  deck.unselectDeck();
               }
            });
            nbr_decks_selected = 1;
         }

         if (args.exact) {
            this.game.toggleButtonEnable("btn_confirm", nbr_decks_selected == count);
         } else {
            this.game.toggleButtonEnable("btn_confirm", nbr_decks_selected <= count);
         }
      };

      this.player_table.getManaDecks().forEach((deck) => {
         if (decks.indexOf(deck) >= 0) {
            deck.setDeckIsSelectable(true);
            deck.onDeckSelectionChanged = () => handleChange(deck);
         } else if (deck.getCards().length > 0) {
            deck
               .getCards()
               .forEach((card) => deck.getCardElement(card).classList.add("bga-cards_disabled-card"));
         }
      });
   }

   onLeavingState(): void {
      this.player_table.getManaDecks().forEach((deck) => {
         deck.setDeckIsSelectable(false);
         deck.onDeckSelectionChanged = null;
      });
      document
         .querySelectorAll(".bga-cards_disabled-card")
         .forEach((value) => value.classList.remove("bga-cards_disabled-card"));
   }

   onUpdateActionButtons(args: SelectManaDeckArgs): void {
      const handleConfirm = () => {
         const decks = this.player_table.getManaDeckWithSpellOver(args.exclude);
         const selected_decks = decks.filter((x) => x.isDeckSelected).map((x) => x.location);

         if (selected_decks.length < args.count) {
            if (!args.exact) {
               const text = _("Are you sure that is how many mana cards you would like to select?");
               this.game.confirmationDialog(text, () => {
                  this.game.actionManager.addArgument(selected_decks.join(","));
                  this.game.actionManager.activateNextAction();
               });
            }
         } else {
            this.game.actionManager.addArgument(selected_decks.join(","));
            this.game.actionManager.activateNextAction();
         }
      };

      const handleIgnore = () => {
         const text = _("Are-you sure you want to ignore this effect?");
         this.game.confirmationDialog(text, args.ignore);
      };

      this.game.addActionButton("btn_confirm", _("Confirm"), handleConfirm);
      if (args.ignore) {
         this.game.addActionButtonRed("btn_ignore", _("Ignore"), handleIgnore);
      }
      this.game.addActionButtonClientCancel();

      if (args.exact) {
         this.game.toggleButtonEnable("btn_confirm", args.count == 0);
      } else {
         this.game.toggleButtonEnable("btn_confirm", true);
      }
   }

   restoreGameState() {
      return new Promise<boolean>((resolve) => {
         this.player_table.getManaDecks().forEach((deck) => {
            deck.setDeckIsSelectable(false);
            deck.onDeckSelectionChanged = null;
         });
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
   ignore?: () => void;
}
