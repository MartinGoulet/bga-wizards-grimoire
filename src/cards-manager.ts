const card_width: number = 120;
const card_height: number = 168;

class SpellCardManager extends CardManager<SpellCard> {
   constructor(public game: WizardsGrimoire) {
      super(game, {
         getId: (card) => `spell-card-${card.id}`,
         setupDiv: (card: SpellCard, div: HTMLElement) => {
            div.classList.add("wg-card");
            div.classList.add("wg-card-spell");
            div.dataset.cardId = "" + card.id;
            div.dataset.type = "" + card.type;
         },
         setupFrontDiv: (card: SpellCard, div: HTMLElement) => {
            div.dataset.type = "" + card.type;
            div.classList.add("wg-card-spell-front");
         },
         setupBackDiv: (card: SpellCard, div: HTMLElement) => {
            div.classList.add("wg-card-spell-back");
         },
         isCardVisible: (card) => card.type !== null && card.type !== undefined,
         cardWidth: card_width,
         cardHeight: card_height
      });
   }
}

class ManaCardManager extends CardManager<ManaCard> {
   constructor(public game: WizardsGrimoire) {
      super(game, {
         getId: (card) => `mana-card-${card.id}`,
         setupDiv: (card: ManaCard, div: HTMLElement) => {
            div.classList.add("wg-card");
            div.classList.add("wg-card-mana");
            div.dataset.cardId = "" + card.id;
            div.dataset.type = "" + card.type;
         },
         setupFrontDiv: (card: ManaCard, div: HTMLElement) => {
            div.dataset.type = "" + card.type;
            div.classList.add("wg-card-mana-front");
         },
         setupBackDiv: (card: ManaCard, div: HTMLElement) => {
            div.classList.add("wg-card-mana-back");
         },
         isCardVisible: (card) => card.type !== null && card.type !== undefined,
         cardWidth: card_width,
         cardHeight: card_height
      });
   }
}
