class SelectManaDiscardStates implements StateHandler {
   constructor(private game: WizardsGrimoire) {}
   onEnteringState(args: SelectManaDiscardArgs): void {
      this.game.tableCenter.moveManaDiscardPile(true);
      const deck = this.game.tableCenter.manaDiscardDisplay;
      const { count, exact } = args;

      deck.setSelectionMode(count == 1 && exact ? "single" : "multiple");

      const handleChange = (selection: ManaCard[], lastChange: ManaCard) => {
         this.game.toggleButtonEnable(
            "btn_confirm",
            exact ? selection.length == count : selection.length <= count,
         );
      };

      deck.onSelectionChange = handleChange;
   }
   onLeavingState(): void {
      this.game.tableCenter.moveManaDiscardPile(false);
      this.game.tableCenter.manaDiscardDisplay.onSelectionChange = null;
   }
   onUpdateActionButtons(args: SelectManaDiscardArgs): void {
      const handleConfirm = () => {
         const selected_card_ids = this.game.tableCenter.manaDiscardDisplay.getSelection().map((x) => x.id);
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
      this.game.disableButton("btn_confirm");

      this.game.addActionButtonClientCancel();
   }
   restoreGameState() {
      return new Promise<boolean>((resolve) => {
         this.game.tableCenter.moveManaDiscardPile(false);
         this.game.tableCenter.manaDiscardDisplay.onSelectionChange = null;
         resolve(true);
      });
   }
}

interface SelectManaDiscardArgs {
   player_id: number;
   count: number;
   exact: boolean;
}
