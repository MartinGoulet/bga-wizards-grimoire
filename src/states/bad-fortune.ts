class BadFortuneStates implements StateHandler {
   private player_table: PlayerTable;
   // private spell_cooldown: ManaDeck;

   private deck_cards: ManaCard[];
   // private spell_cards: ManaCard[];

   private mana_count: number = 0;

   constructor(private game: WizardsGrimoire) {}

   onEnteringState(args: BadFortuneArgs): void {
      if (!this.game.isCurrentPlayerActive()) return;

      this.mana_count = this.game.tableCenter.manaRevealed.getCards().length;
      this.deck_cards = [];
      // this.spell_cards = [];
      this.player_table = this.game.getCurrentPlayerTable();
      // this.spell_cooldown = this.player_table.mana_cooldown[Number(args.spell.location_arg)];

      const handleManaRevealedClick = (card: ManaCard) => {
         // if (this.game.getPower(card) == 1) {
         //    this.spell_cooldown.addCard(card);
         //    this.spell_cards.push(card);
         // } else {
         this.game.tableCenter.manaDeck.addCard(card);
         this.deck_cards.push(card);
         // }
         this.game.enableButton("btnCancel", "gray");
         this.game.toggleButtonEnable("btnConfirm", this.isAllManaCardDistributed(), "blue");
      };

      const handleReturn = (cards: ManaCard[]) => {
         if (cards.length == 0) return;
         const card = cards.pop();
         this.game.tableCenter.manaRevealed.addCard(card);
         this.game.toggleButtonEnable(
            "btnCancel",
            this.deck_cards.length > 0, // this.spell_cards.length > 0 || this.deck_cards.length > 0,
            "gray",
         );
         this.game.toggleButtonEnable("btnConfirm", this.isAllManaCardDistributed(), "blue");
      };

      this.game.tableCenter.manaRevealed.onCardClick = handleManaRevealedClick;
      this.game.tableCenter.manaDeck.onCardClick = () => handleReturn(this.deck_cards);
      // this.spell_cooldown.onCardClick = () => handleReturn(this.spell_cards);
   }

   onLeavingState(): void {
      this.deck_cards = [];
      // this.spell_cards = [];
      this.game.tableCenter.manaRevealed.onCardClick = null;
      this.game.tableCenter.manaDeck.onCardClick = null;
      // this.spell_cooldown.onCardClick = null;
   }

   onUpdateActionButtons(args: BadFortuneArgs): void {
      const handleConfirm = () => {
         if (!this.isAllManaCardDistributed()) return;
         // const cards = [...this.spell_cards, ...this.deck_cards];
         const cards = [...this.deck_cards];
         this.game.actionManager.addArgument(cards.map((x) => x.id).join(","));
         this.game.actionManager.activateNextAction();
      };

      const handleCancel = () => {
         // this.game.tableCenter.manaRevealed.addCards(this.spell_cards.splice(0, this.spell_cards.length));
         this.game.tableCenter.manaRevealed.addCards(this.deck_cards.splice(0, this.deck_cards.length));
         this.game.disableButton("btnConfirm");
      };

      this.game.addActionButton("btnConfirm", _("Confirm"), handleConfirm);
      this.game.addActionButtonGray("btnCancel", _("Cancel"), handleCancel);
      this.game.disableButton("btnConfirm");
      this.game.disableButton("btnCancel");
   }

   restoreGameState() {
      return new Promise<boolean>((resolve) => resolve(true));
   }

   isAllManaCardDistributed() {
      return this.mana_count == this.deck_cards.length;
      // return this.spell_cards.length + this.deck_cards.length == 3;
   }
}

interface BadFortuneArgs {
   spell: SpellCard;
}
