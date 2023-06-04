const EIGHT_CARDS_SLOT = [1, 5, 2, 6, 3, 7, 4, 8];
const TEN_CARDS_SLOT = [1, 6, 2, 7, 3, 8, 4, 9, 5, 10];

class TableCenter {
   public spellDeck: Deck<SpellCard>;
   public spellDiscard: Deck<SpellCard>;

   public manaDeck: Deck<ManaCard>;
   public manaDiscard: Deck<ManaCard>;

   public spellPool: SlotStock<SpellCard>;

   constructor(private game: WizardsGrimoire) {
      const settings = {
         cardNumber: 0,
         counter: {
            hideWhenEmpty: false,
         },
      };
      this.spellDeck = new MyDeck(game.spellsManager, document.getElementById("spell-deck"), settings);
      this.spellDiscard = new MyDeck(game.spellsManager, document.getElementById("spell-discard"), settings);
      this.manaDiscard = new MyDeck(game.manasManager, document.getElementById("mana-discard"), settings);
      this.manaDeck = new MyDeck(game.manasManager, document.getElementById("mana-deck"), settings);
      this.spellPool = new SlotStock(game.spellsManager, document.getElementById("spell-pool"), {
         slotsIds: game.gamedatas.slot_count == 8 ? EIGHT_CARDS_SLOT : TEN_CARDS_SLOT,
         slotClasses: ["wg-spell-slot"],
         mapCardToSlot: (card) => card.location_arg,
         direction: "column",
      });

      game.gamedatas.spells.deck.forEach((card) => {
         this.spellDeck.addCard({ ...card, isHidden: true });
      });
      game.gamedatas.manas.deck.forEach((card) => {
         this.manaDeck.addCard({ ...card, isHidden: true });
      });
      this.spellPool.addCards(game.gamedatas.slot_cards);
   }

   public onRefillSpell(card: SpellCard) {
      const topHiddenCard = { ...card, isHidden: true };
      this.spellDeck.setCardNumber(this.spellDeck.getCardNumber(), topHiddenCard);
      this.spellPool.addCard(card);
   }
}
