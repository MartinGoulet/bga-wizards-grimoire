class MyDeck<T> extends Deck<T> {
   constructor(protected manager: CardManager<T>, protected element: HTMLElement, settings: DeckSettings<T>) {
      super(manager, element, settings);
   }

   public addCard(card: T, animation?: CardAnimation<T>, settings?: AddCardToDeckSettings): Promise<boolean> {
      if (!settings) {
         settings = {};
      }
      settings.index = 0;
      super.addCard(card, animation, settings);
   }
}
