class BelchStates implements StateHandler {
   private deck_cards: ManaCard[];
	private discard_cards: ManaCard[];

   private mana_count: number = 0;

   constructor(private game: Game) {}

   onEnteringState(args: BadFortuneArgs): void {
      if (!this.game.isCurrentPlayerActive()) return;

		const { manaRevealed, manaDeck, manaDiscard } = this.game.tableCenter;

      this.mana_count = manaRevealed.getCards().length;
      this.deck_cards = [];
      this.discard_cards = [];

		manaRevealed.setSelectionMode('single');
		manaRevealed.onSelectionChange = (selection: ManaCard[]) => {
         this.game.toggleButtonEnable('btnMoveToDiscard', selection.length > 0, 'blue');
			this.game.toggleButtonEnable('btnMoveToManaDeck', selection.length > 0, 'blue');
      };

      const handleReturn = async (cards: ManaCard[]) => {
         if (cards.length == 0) return;
         const card = cards.pop();
         await this.game.tableCenter.manaRevealed.addCard(card);
         this.game.toggleButtonEnable(
            "btnCancel",
            this.deck_cards.length > 0, // this.spell_cards.length > 0 || this.deck_cards.length > 0,
            "gray",
         );
      };

      this.game.tableCenter.manaDeck.onCardClick = () => handleReturn(this.deck_cards);
		this.game.tableCenter.manaDiscard.onCardClick = () => handleReturn(this.discard_cards);
   }

   onLeavingState(): void {
      this.deck_cards = [];
      this.game.tableCenter.manaRevealed.onCardClick = null;
      this.game.tableCenter.manaDeck.onCardClick = null;
   }

   onUpdateActionButtons(args: BadFortuneArgs): void {


		const handleDiscard = async () => {
			const selectedCard = this.game.tableCenter.manaRevealed.getSelection().pop();
			if(!selectedCard) return;
			this.discard_cards.push(selectedCard);
			await this.game.tableCenter.manaDiscard.addCard(selectedCard);
			this.game.toggleButtonEnable('btnConfirm', this.deck_cards.length + this.discard_cards.length == this.mana_count, 'blue');
		};

		const handleReturnToDeck = async () => {
			const selectedCard = this.game.tableCenter.manaRevealed.getSelection().pop();
			if(!selectedCard) return;
			this.deck_cards.push(selectedCard);
			await this.game.tableCenter.manaDeck.addCard(selectedCard);
			this.game.toggleButtonEnable('btnConfirm', this.deck_cards.length + this.discard_cards.length == this.mana_count, 'blue');
		};

		this.game.statusBar.addActionButton(_("Move to Mana Deck"), handleReturnToDeck, {
			id: 'btnMoveToManaDeck',
		});

		this.game.statusBar.addActionButton(_("Move to Discard"), handleDiscard, {
			id: 'btnMoveToDiscard',
		});

		this.game.disableButton('btnMoveToDiscard');
		this.game.disableButton('btnMoveToManaDeck');

      const handleConfirm = () => {
			if (this.deck_cards.length + this.discard_cards.length != this.mana_count) return;
         this.game.actionManager.addArgument(this.deck_cards.map((x) => x.id).join(","));
         this.game.actionManager.addArgument(this.discard_cards.map((x) => x.id).join(","));
         this.game.actionManager.activateNextAction();
      };

      const handleCancel = async () => {
         await this.game.tableCenter.manaRevealed.addCards(this.deck_cards.splice(0, this.deck_cards.length));
         await this.game.tableCenter.manaRevealed.addCards(this.discard_cards.splice(0, this.discard_cards.length));
			this.deck_cards = [];
			this.discard_cards = [];
         this.game.disableButton("btnConfirm");
      };

      this.game.addActionButton("btnConfirm", _("Confirm"), handleConfirm);
      this.game.addActionButtonGray("btnCancel", _("Reset"), handleCancel);
      this.game.disableButton("btnConfirm");
   }

   restoreGameState() {
      return new Promise<boolean>((resolve) => resolve(true));
   }
}

interface BadFortuneArgs {
   spell: SpellCard;
}
