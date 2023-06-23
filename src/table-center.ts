const EIGHT_CARDS_SLOT = [1, 5, 2, 6, 3, 7, 4, 8];
const TEN_CARDS_SLOT = [1, 6, 2, 7, 3, 8, 4, 9, 5, 10];

class TableCenter {
   public spellDeck: HiddenDeck<SpellCard>;
   public spellDiscard: VisibleDeck<SpellCard>;

   public manaDeck: HiddenDeck<ManaCard>;
   public manaDiscard: VisibleDeck<ManaCard>;

   public spellPool: SlotStock<SpellCard>;

   public manaDiscardDisplay: LineStock<ManaCard>;
   public manaRevealed: LineStock<ManaCard>;
   public basicAttack: LineStock<ManaCard>;

   constructor(private game: WizardsGrimoire) {
      dojo.place(`<span class="wg-title">${_("Basic Attack")}</span>`, "basic-attack-wrapper");
      dojo.place(`<div id="basic-attack"></div>`, "basic-attack-wrapper");
      dojo.place(`<span class="wg-title">${_("Revealed Mana")}</span>`, "mana-revealed-wrapper");
      dojo.place(`<div id="mana-revealed"></div>`, "mana-revealed-wrapper");

      this.spellDeck = new HiddenDeck(game.spellsManager, document.getElementById("spell-deck"));
      this.manaDeck = new HiddenDeck(game.manasManager, document.getElementById("mana-deck"));
      this.spellDiscard = new VisibleDeck(game.spellsManager, document.getElementById("spell-discard"));
      this.manaDiscard = new VisibleDeck(game.manasManager, document.getElementById("mana-discard"));

      this.spellPool = new SlotStock(game.spellsManager, document.getElementById("spell-pool"), {
         slotsIds: game.gamedatas.slot_count == 8 ? EIGHT_CARDS_SLOT : TEN_CARDS_SLOT,
         slotClasses: ["wg-spell-slot"],
         mapCardToSlot: (card) => card.location_arg,
         direction: "column",
      });

      this.manaDiscardDisplay = new LineStock(
         game.manasManager,
         document.getElementById("mana-discard-display"),
         { gap: "2px" },
      );
      this.manaRevealed = new LineStock(game.manasManager, document.getElementById("mana-revealed"), {
         gap: "10px",
      });
      this.basicAttack = new LineStock(game.manasManager, document.getElementById("basic-attack"));

      this.manaDiscardDisplay.setSelectionMode("multiple");

      game.gamedatas.spells.deck.forEach((card) => {
         this.spellDeck.addCard({ ...card, isHidden: true });
      });
      game.gamedatas.manas.deck.forEach((card) => {
         this.manaDeck.addCard({ ...card, isHidden: true });
      });
      game.gamedatas.spells.discard.forEach((card) => {
         this.spellDiscard.addCard(card);
      });
      this.basicAttack.addCards(game.gamedatas.manas.attack);
      this.manaRevealed.addCards(game.gamedatas.manas.revealed);

      const sortAscending = (a: ManaCard, b: ManaCard) => Number(a.location_arg) - Number(b.location_arg);
      game.gamedatas.manas.discard.sort(sortAscending).forEach((card) => {
         this.manaDiscard.addCard(card);
      });
      this.spellPool.addCards(game.gamedatas.slot_cards);
   }

   public async shuffleManaDeck(cards: ManaCard[]) {
      await this.manaDeck.addCards(cards);
      await this.manaDeck.shuffle(8);
   }

   public moveManaDiscardPile(toDisplay: boolean) {
      this.manaDiscardDisplay.unselectAll();
      if (toDisplay) {
         const cards = [...this.manaDiscard.getCards()];
         this.manaDiscardDisplay.addCards(cards);
      } else {
         // const cards = [...this.manaDiscardDisplay.getCards()];
         // this.manaDiscard.addCards(cards);
         this.manaDiscardDisplay
            .getCards()
            .sort((x, y) => x.location_arg - y.location_arg)
            .forEach((card) => {
               this.manaDiscard.addCard(card);
            });
      }
   }

   public onRefillSpell(card: SpellCard) {
      const topHiddenCard = { ...card, isHidden: true };
      this.spellDeck.setCardNumber(this.spellDeck.getCardNumber(), topHiddenCard);
      this.spellPool.addCard(card);
   }
}
