class SelectManaStates implements StateHandler {
   private player_table: PlayerTable;

   constructor(private game: WizardsGrimoire) {}

   onEnteringState(args: SelectManaArgs): void {
      if (!this.game.isCurrentPlayerActive()) return;

      const { card, player_id, count } = args;
      this.player_table = this.game.getPlayerTable(player_id);

      const handleChange = () => {
         const nbr_cards_selected = this.getManaDecks().filter(
            (x) => x.deck.getSelection().length > 0,
         ).length;
         this.game.toggleButtonEnable("btn_confirm", nbr_cards_selected <= count);
      };

      this.getManaDecks().forEach(({ position, deck }) => {
         if (position != card.location_arg && deck.getCardNumber() > 0) {
            deck.setSelectionMode("single");
            deck.onSelectionChange = handleChange;
         }
      });
   }

   onLeavingState(): void {
      this.getManaDecks().forEach(({ deck }) => {
         deck.setSelectionMode("none");
         deck.onSelectionChange = null;
      });
   }

   onUpdateActionButtons(args: SelectManaArgs): void {
      const handleConfirm = () => {
         const selected_card_ids = this.getManaDecks()
            .filter((x) => x.deck.getSelection().length > 0)
            .map((x) => x.deck.getSelection()[0].id)
            .join(",");

         this.game.actionManager.addArgument(selected_card_ids).activateNextAction();
      };
      this.game.addActionButton("btn_confirm", _("Confirm"), handleConfirm);
      this.game.addActionButtonClientCancel();
   }

   private getManaDecks() {
      return [1, 2, 3, 4, 5, 6].map((position: number) => {
         return {
            position,
            deck: this.player_table.mana_cooldown[position],
         };
      });
   }
}

interface SelectManaArgs {
   player_id: number;
   card: SpellCard;
   count: number;
}
