class HiddenDeck<T extends Card> extends Deck<T> {
   constructor(protected manager: CardManager<T>, protected element: HTMLElement) {
      super(manager, element, {
         cardNumber: 0,
         counter: {
            hideWhenEmpty: false,
         },
         autoRemovePreviousCards: false,
      });
   }

   public addCard(card: T, animation?: CardAnimation<T>, settings?: AddCardToDeckSettings): Promise<boolean> {
      settings = settings ?? {};
      settings.index = settings.index ?? 0;
      return super.addCard(card, animation, settings);
   }
}

class VisibleDeck<T extends Card> extends Deck<T> {
   constructor(protected manager: CardManager<T>, protected element: HTMLElement) {
      super(manager, element, {
         cardNumber: 0,
         counter: {
            hideWhenEmpty: false,
         },
         autoRemovePreviousCards: false,
      });
   }
}

class ManaDeck extends Deck<ManaCard> {
   private isDeckSelectable: boolean = false;

   private _isDeckSelected: boolean = false;
   public get isDeckSelected(): boolean {
      return this._isDeckSelected;
   }
   public set isDeckSelected(value: boolean) {
      this._isDeckSelected = value;
      this.element.classList.toggle("wg-deck-selected", this.isDeckSelected);
   }

   /**
    * Called when deck selection change.
    */
   public onDeckSelectionChanged?: () => void;

   constructor(manager: CardManager<ManaCard>, element: HTMLElement, public readonly location: number) {
      super(manager, element, {
         cardNumber: 0,
         counter: {},
         autoRemovePreviousCards: false,
      });
   }

   public addCard(
      card: ManaCard,
      animation?: CardAnimation<ManaCard>,
      settings?: AddCardToDeckSettings,
   ): Promise<boolean> {
      settings = settings ?? {};
      settings.index = Number(card.location_arg);
      return super.addCard(card, animation, settings);
   }

   public setDeckIsSelectable(value: boolean) {
      this.isDeckSelectable = value;
      if (!this.isDeckSelectable && this.isDeckSelected) {
         this.isDeckSelected = false;
      }
   }

   protected bindClick(): void {
      this.element?.addEventListener("click", (event) => {
         if (this.isDeckSelectable) {
            this.deckClick();
         } else {
            const cardDiv = (event.target as HTMLElement).closest(".card");
            if (!cardDiv) {
               return;
            }
            const card = this.cards.find((c) => this.manager.getId(c) == cardDiv.id);
            if (!card) {
               return;
            }
            this.cardClick(card);
         }
      });
   }

   private deckClick() {
      if (this.isDeckSelectable) {
         this.isDeckSelected = !this.isDeckSelected;
         this.onDeckSelectionChanged?.();
      }
   }

   public forceSelected() {
      this.element?.classList.add("wg-deck-was-selected");
   }

   public unselectDeck() {
      this.isDeckSelected = false;
      this.element.classList.toggle("wg-deck-selected", this.isDeckSelected);
   }
}

class SpellRepertoire extends SlotStock<SpellCard> {
   constructor(
      protected manager: CardManager<SpellCard>,
      protected element: HTMLElement,
      private player_table: PlayerTable,
   ) {
      super(manager, element, {
         slotsIds: [1, 2, 3, 4, 5, 6],
         slotClasses: ["wg-spell-slot"],
         mapCardToSlot: (card) => card.location_arg,
      });
   }
   public addCard(
      card: SpellCard,
      animation?: CardAnimation<SpellCard>,
      settings?: AddCardToSlotSettings,
   ): Promise<boolean> {
      this.setDataset(card, true);
      return super.addCard(card, animation, settings);
   }

   public removeCard(card: SpellCard, settings?: RemoveCardSettings): void {
      this.setDataset(card, false);
      return super.removeCard(card, settings);
   }

   private setDataset(card: SpellCard, value: boolean) {
      const card_type = this.player_table.game.getCardType(card);
      const element = this.element.parentElement.parentElement;
      switch (card_type["class"]) {
         case "BattleVision":
            element.dataset.battle_vision = "" + value;
            break;
         case "Puppetmaster":
            element.dataset.puppetmaster = "" + value;
            break;
         case "SecretOath":
            element.dataset.secret_oath = "" + value;
            break;
      }
   }
}

class Hand extends LineStock<ManaCard> {
   constructor(manager: CardManager<ManaCard>, element: HTMLElement, protected current_player: boolean) {
      super(manager, element, {
         center: true,
         wrap: "wrap",
         sort: sortFunction("type", "type_arg"),
      });
   }

   public addCard(
      card: ManaCard,
      animation?: CardAnimation<ManaCard>,
      settings?: AddCardSettings,
   ): Promise<boolean> {
      const copy: ManaCard = { ...card, isHidden: !this.current_player };
      if (!this.current_player) {
         copy.type = null;
      }
      return super.addCard(copy, animation, settings);
   }
}
