class EclipseStates implements StateHandler {
   private mana_cards: ManaCard[] = [];
   private player_ids: number[] = [];
   private mana_cooldown_position: number[] = [];

   constructor(private game: WizardsGrimoire) {}

   onEnteringState(args: EclipseArgs): void {
      if (!this.game.isCurrentPlayerActive()) return;

      this.resetVariables();

      const revealed_zone = this.game.tableCenter.manaRevealed;
      const player_mana_cooldown = this.game.getCurrentPlayerTable().getManaDeckWithSpellOver();
      const opponent_mana_cooldown = this.game
         .getPlayerTable(this.game.getOpponentId())
         .getManaDeckWithSpellOver();

      const handleDeckSelection = (deck: ManaDeck, position: number, player_id: number) => {
         deck.unselectDeck();
         if (revealed_zone.getSelection().length !== 1) return;
         const card = { ...revealed_zone.getSelection()[0] };
         card.location_arg = deck.getCards().length + 1;
         deck.addCard(card);

         this.mana_cards.push(card);
         this.player_ids.push(player_id);
         this.mana_cooldown_position.push(position);

         this.game.toggleButtonEnable("btnConfirm", this.mana_cards.length === 3, "blue");
         this.game.enableButton("btnCancel", "gray");
      };

      for (let index = 0; index < player_mana_cooldown.length; index++) {
         const deck = player_mana_cooldown[index];
         deck.onDeckSelectionChanged = () => handleDeckSelection(deck, index + 1, this.game.getPlayerId());
         deck.setDeckIsSelectable(true);
      }

      for (let index = 0; index < opponent_mana_cooldown.length; index++) {
         const deck = opponent_mana_cooldown[index];
         deck.onDeckSelectionChanged = () => handleDeckSelection(deck, index + 1, this.game.getOpponentId());
         deck.setDeckIsSelectable(true);
      }

      revealed_zone.setSelectionMode("single");
   }

   onLeavingState(): void {
      this.game.tableCenter.manaRevealed.setSelectionMode("none");

      const decks = [
         ...this.game.getCurrentPlayerTable().getManaDecks(),
         ...this.game.getPlayerTable(this.game.getOpponentId()).getManaDecks(),
      ];

      decks.forEach((deck) => {
         deck.unselectDeck();
         deck.setDeckIsSelectable(false);
         deck.onDeckSelectionChanged = null;
      });
      this.resetVariables();
   }

   onUpdateActionButtons(args: EclipseArgs): void {
      const handleConfirm = () => {
         if (this.mana_cards.length !== 3) return;

         this.game.actionManager.addArgument(this.mana_cards.map((card) => card.id).join(","));
         this.game.actionManager.addArgument(this.player_ids.join(","));
         this.game.actionManager.addArgument(this.mana_cooldown_position.join(","));
         this.game.actionManager.activateNextAction();
      };
      const handleCancel = () => {
         this.game.tableCenter.manaRevealed.addCards(this.mana_cards);
         this.resetVariables();
         this.game.disableButton("btnConfirm");
         this.game.disableButton("btnCancel");
      };

      this.game.addActionButtonDisabled("btnConfirm", _("Confirm"), handleConfirm);
      this.game.addActionButtonDisabled("btnCancel", _("Cancel"), handleCancel);
   }

   restoreGameState(): Promise<boolean> {
      return new Promise<boolean>((resolve) => resolve(true));
   }

   private resetVariables() {
      this.mana_cards = [];
      this.player_ids = [];
      this.mana_cooldown_position = [];
   }
}

interface EclipseArgs {}
