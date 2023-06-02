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

            if (div.childNodes.length == 1) {
               div.insertAdjacentHTML(
                  "afterbegin",
                  `<div class="help-marker">
                  <svg class="feather feather-help-circle" fill="white" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" x2="12.01" y1="17" y2="17"></line></svg>
               </div>`
               );
            }
         },
         setupBackDiv: (card: SpellCard, div: HTMLElement) => {
            div.classList.add("wg-card-spell-back");
         },
         isCardVisible: (card) => {
            if (card.isHidden == true) {
               return false;
            } else {
               return card.type !== null && card.type !== undefined;
            }
         },
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
         isCardVisible: (card) => {
            if (card.isHidden == true) {
               return false;
            } else {
               return card.type !== null && card.type !== undefined;
            }
         },
         cardWidth: card_width,
         cardHeight: card_height
      });
   }
}
