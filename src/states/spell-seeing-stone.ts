class SpellSeeingStoneState implements StateHandler {
   private card_order: SpellCard[] = [];
   constructor(private game: Game) {}

   onEnteringState(args: SpellSeeingStoneArgs): void {
      if (!this.game.isCurrentPlayerActive()) return;
      log("Entering spell seeing stone state", args);

      (async () => {
         await this.game.wait(500);
         this.card_order = [];
         const spellRevealed = this.game.tableCenter.spellRevealed;

         await spellRevealed.addCards(
            args._private.cards,
            {
               fromStock: this.game.tableCenter.spellDeck,
            },
            undefined,
            750
         );

         spellRevealed.onCardClick = async (card: SpellCard) => {
            this.card_order.push(card);
            const newCard = { id: card.id, isHidden: true } as SpellCard;
            await this.game.tableCenter.spellDeck.addCard(newCard);
            this.game.toggleButtonEnable('btnReorder', spellRevealed.getCards().length === 0);
            this.game.toggleButtonEnable('btnReorderReplace', spellRevealed.getCards().length === 1);
         };
      })();
   }

   onLeavingState(): void {}

   onUpdateActionButtons(args: SpellSeeingStoneArgs): void {
      const spellRevealed = this.game.tableCenter.spellRevealed;

      const handleReorder = async () => {
         this.game.bgaPerformAction("actSelectSpellSeeingStone", {
            cardOrder: this.card_order.map((card) => card.id).join(","),
            replaceSpellId: 0,
         });
      }

      const handleReorderReplace = async () => {
         this.game.bgaPerformAction("actSelectSpellSeeingStone", {
            cardOrder: this.card_order.map((card) => card.id).join(","),
            replaceSpellId: spellRevealed.getCards()[0].id,
         });
      }

      this.game.addActionButton("btnReorderReplace", _("Replace"), handleReorderReplace);
      this.game.addActionButton("btnReorder", _("Reorder only"), handleReorder);

      this.game.toggleButtonEnable("btnReorder", false);
      this.game.toggleButtonEnable("btnReorderReplace", false);

      this.game.addActionButtonClientCancel();
   }

   restoreGameState(): Promise<boolean> {
      return Promise.resolve(true);
   }
}

interface SpellSeeingStoneArgs {
   _private: {
      cards: SpellCard[];
   };
}
