class HiddenDeck<T extends Card> extends Deck<T> {
   constructor(protected manager: CardManager<T>, protected element: HTMLElement, settings: DeckSettings<T>) {
      super(manager, element, settings);
   }

   public addCard(card: T, animation?: CardAnimation<T>, settings?: AddCardToDeckSettings): Promise<boolean> {
      settings = settings ?? {};
      settings.index = settings.index ?? 0;
      return super.addCard(card, animation, settings);
   }
}

class VisibleDeck<T extends Card> extends Deck<T> {
   constructor(protected manager: CardManager<T>, protected element: HTMLElement, settings: DeckSettings<T>) {
      super(manager, element, settings);
   }

   public addCard(card: T, animation?: CardAnimation<T>, settings?: AddCardToDeckSettings): Promise<boolean> {
      settings = settings ?? {};
      settings.index = settings.index ?? card.location_arg;
      return super.addCard(card, animation, settings);
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
