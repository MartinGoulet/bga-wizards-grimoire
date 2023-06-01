const EIGHT_CARDS_SLOT = [1, 5, 2, 6, 3, 7, 4, 8];
const TEN_CARDS_SLOT = [1, 6, 2, 7, 3, 8, 4, 9, 5, 10];

class TableCenter {
   public spellDeck: Deck<SpellCard>;
   public spellDiscard: Deck<SpellCard>;

   public manaDeck: Deck<ManaCard>;
   public manaDiscard: Deck<ManaCard>;

   public spellPool: SlotStock<SpellCard>;

   constructor(private game: WizardsGrimoire) {
      this.spellDeck = new Deck(game.spellsManager, document.getElementById("spell-deck"), {
         cardNumber: game.gamedatas.spells.deck_count,
         topCard: { id: 100000 } as SpellCard, //hidden
         counter: {}
      });
      this.spellDiscard = new Deck(game.spellsManager, document.getElementById("spell-discard"), {
         cardNumber: game.gamedatas.spells.discard_count,
         counter: {}
      });

      this.manaDiscard = new Deck(game.manasManager, document.getElementById("mana-discard"), {
         cardNumber: game.gamedatas.manas.discard_count,
         counter: {}
      });
      this.manaDeck = new Deck(game.manasManager, document.getElementById("mana-deck"), {
         cardNumber: game.gamedatas.manas.deck_count,
         topCard: { id: 100001 } as SpellCard, //hidden
         counter: {}
      });

      this.spellPool = new SlotStock(game.spellsManager, document.getElementById("spell-pool"), {
         slotsIds: game.gamedatas.slot_count == 8 ? EIGHT_CARDS_SLOT : TEN_CARDS_SLOT,
         slotClasses: ["wg-spell-slot"],
         mapCardToSlot: (card) => card.location_arg,
         direction: "column"
      });

      this.spellPool.addCards(game.gamedatas.slot_cards);
   }
}
