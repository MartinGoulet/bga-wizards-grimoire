const EIGHT_CARDS_SLOT = [1, 5, 2, 6, 3, 7, 4, 8];
const TEN_CARDS_SLOT = [1, 6, 2, 7, 3, 8, 4, 9, 5, 10];

class DiscardPile extends VisibleDeck<ManaCard> {
   public onAddCard: (card: ManaCard) => void;
   public onRemoveCard: (card: ManaCard) => void;

   constructor(manager: CardManager<ManaCard>, element: HTMLElement) {
      super(manager, element);
   }

   public addCard(
      card: ManaCard,
      animation?: CardAnimation<ManaCard>,
      settings?: AddCardToDeckSettings,
   ): Promise<boolean> {
      const promise = super.addCard(card, animation, settings);
      this.onAddCard({ ...card });
      return promise;
   }

   public removeCard(card: ManaCard, settings?: RemoveCardSettings): void {
      const copy = { ...card };
      super.removeCard(card, settings);
      this.onRemoveCard(copy);
   }
}

class TableCenter {
   public spellDeck: HiddenDeck<SpellCard>;
   public spellDiscard: VisibleDeck<SpellCard>;

   public manaDeck: HiddenDeck<ManaCard>;
   public manaDiscard: DiscardPile;

   public spellPool: SlotStock<SpellCard>;

   public manaDiscardDisplay: LineStock<ManaCard>;
   public manaRevealed: LineStock<ManaCard>;
   public basicAttack: LineStock<ManaCard>;

   public mana_counter: { [number: number]: ebg.counter } = {};

   constructor(private game: WizardsGrimoire) {
      this.place(`<span class="wg-title">${_("Basic Attack")}</span>`, "basic-attack-wrapper");
      this.place(`<div id="basic-attack"></div>`, "basic-attack-wrapper");
      this.place(`<span class="wg-title">${_("Revealed Mana")}</span>`, "mana-revealed-wrapper");
      this.place(`<div id="mana-revealed"></div>`, "mana-revealed-wrapper");
      this.place(`<span class="wg-title">${_("Discard")}</span>`, "mana-discard-display-wrapper");
      this.place(`<div id="mana-discard-display"></div>`, "mana-discard-display-wrapper");

      this.spellDeck = new HiddenDeck(game.spellsManager, document.getElementById("spell-deck"));
      this.manaDeck = new HiddenDeck(game.manasManager, document.getElementById("mana-deck"));
      this.spellDiscard = new VisibleDeck(game.spellsManager, document.getElementById("spell-discard"));
      this.manaDiscard = new DiscardPile(game.manasManager, document.getElementById("mana-discard"));

      this.spellPool = new SlotStock(game.spellsManager, document.getElementById("spell-pool"), {
         slotsIds: game.gamedatas.slot_count == 8 ? EIGHT_CARDS_SLOT : TEN_CARDS_SLOT,
         slotClasses: ["wg-spell-slot"],
         mapCardToSlot: (card) => card.location_arg,
         direction: "column",
      });

      this.manaDiscardDisplay = new LineStock(
         game.discardManager,
         document.getElementById("mana-discard-display"),
         { gap: "2px", center: false },
      );

      this.setupManaCounter();

      this.manaDiscard.onAddCard = (card: ManaCard) => {
         this.updateManaCounter();
         this.manaDiscardDisplay.addCard({ ...card });
      };
      this.manaDiscard.onRemoveCard = (card: ManaCard) => {
         this.updateManaCounter();
         this.manaDiscardDisplay.removeCard({ ...card });
      };

      this.manaRevealed = new LineStock(game.manasManager, document.getElementById("mana-revealed"), {
         gap: "10px",
      });
      this.basicAttack = new LineStock(game.manasManager, document.getElementById("basic-attack"));

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

      document
         .getElementById("mana-discard")
         .insertAdjacentHTML("afterbegin", `<div id="eye-icon-discard" class="eye-icon discard"></div>`);

      const tableElement = document.getElementById("table");

      document.getElementById("eye-icon-discard").addEventListener("click", () => {
         tableElement.dataset.display_discard =
            tableElement.dataset.display_discard == "false" ? "true" : "false";
      });

      this.game.addTooltipHtml("mana-deck", _("Mana Deck"), 1000);
      this.game.addTooltipHtml("mana-discard", _("Mana Discard"), 1000);
      this.game.addTooltipHtml("spell-deck", _("Spell Deck"), 1000);
      this.game.addTooltipHtml("spell-discard", _("Spell Discard"), 1000);
   }

   private setupManaCounter() {
      const manaInfos = [1, 2, 3, 4].map((number) => {
         return `<div class="mana-discard-info-line">
            <div id="mana-counter-wrapper-${number}" class="wg-icon-log i-mana-x">${number}</div><div>x</div><div id="mana-counter-${number}">0</div>
         </div>`;
      });

      document.getElementById("mana-discard-info").insertAdjacentHTML("afterbegin", manaInfos.join(""));

      [1, 2, 3, 4].forEach((number) => {
         this.mana_counter[number] = new ebg.counter();
         this.mana_counter[number].create(`mana-counter-${number}`);
         this.game.addTooltipHtml(
            `mana-counter-wrapper-${number}`,
            _("Number of ${mana_value} in the discard pile").replace("${mana_value}", number.toString()),
            1000,
         );
      });
   }

   public async shuffleManaDeck(cards: ManaCard[]) {
      await this.manaDeck.addCards(cards);
      await this.manaDeck.shuffle(8);
   }

   public moveManaDiscardPile(toDisplay: boolean) {
      const elTable = document.getElementById("table");
      elTable.dataset.display_discard = toDisplay ? "true" : "false";
      this.manaDiscardDisplay.setSelectionMode(toDisplay ? "multiple" : "none");
   }

   public onRefillSpell(card: SpellCard) {
      const topHiddenCard = { ...card, isHidden: true };
      this.spellDeck.setCardNumber(this.spellDeck.getCardNumber(), topHiddenCard);
      this.spellPool.addCard(card);
   }

   private place(html: string, element: string) {
      document.getElementById(element).insertAdjacentHTML("beforeend", html);
   }

   private toggleDisplayDiscard() {
      const elTable = document.getElementById("table");
      elTable.dataset.display_discard = elTable.dataset.display_discard == "false" ? "true" : "false";
   }

   private updateManaCounter() {
      [1, 2, 3, 4].forEach((number) => {
         const cards = this.manaDiscard.getCards().filter((card) => card.type == number.toString());
         this.mana_counter[number].toValue(cards.length);
      });
   }
}
