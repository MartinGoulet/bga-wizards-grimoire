class TableCenter {
   public spellDeck: Deck<SpellCard>;
   public spellDiscard: Deck<SpellCard>;

   public manaDeck: Deck<ManaCard>;
   public manaDiscard: Deck<ManaCard>;

   constructor(private game: WizardsGrimoire) {
      this.spellDeck = new Deck(game.spellsManager, document.getElementById("spell-deck"), {
         cardNumber: 60,
         topCard: { id: 100000 } as SpellCard, //hidden
         counter: {}
      });
      this.spellDiscard = new Deck(game.spellsManager, document.getElementById("spell-discard"), {
         cardNumber: 0,
         counter: {}
      });

      this.manaDiscard = new Deck(game.manasManager, document.getElementById("mana-discard"), {
         cardNumber: 0,
         counter: {}
      });
      this.manaDeck = new Deck(game.manasManager, document.getElementById("mana-deck"), {
         cardNumber: 60,
         topCard: { id: 100001 } as SpellCard, //hidden
         counter: {}
      });
   }
}
