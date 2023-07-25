const card_width: number = 110;
const card_height: number = 154;

function formatGametext2(rawText: string) {
   if (!rawText) return "";
   let value = rawText.replace(":", ":<br />");
   return "<p>" + value + "</p>";
}

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
            div.id = `${this.getId(card)}-front`;
            div.dataset.type = "" + card.type;
            div.classList.add("wg-card-spell-front");

            if (div.childNodes.length == 1 && card.type) {
               const card_type = this.game.getCardType(card);
               const { name, description } = card_type;
               const gametext = formatGametext2(_(description));

               div.insertAdjacentHTML(
                  "afterbegin",
                  `<div class="wg-card-gametext">
                     <div class="wg-card-gametext-title">${_(name)}</div>
                     <div class="wg-card-gametext-divider"></div>
                     <div class="wg-card-gametext-text">${_(gametext)}</div>
                  </div>`,
               );
               const helpMarkerId = `${this.getId(card)}-help-marker`;
               div.insertAdjacentHTML(
                  "afterbegin",
                  `<div id="${helpMarkerId}" class="help-marker">
                     <i class="fa fa-search" style="color: white"></i>
                  </div>`,
               );

               game.setTooltip(`${this.getId(card)}-front`, this.getTooltip(card));
               document.getElementById(helpMarkerId).addEventListener("click", (evt) => {
                  evt.stopPropagation();
                  evt.preventDefault();
                  this.game.modal.display(card);
               });
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
         cardHeight: card_height,
      });
   }

   getCardById(id: number) {
      return this.getCardStock({ id } as SpellCard)
         .getCards()
         .find((x) => x.id == id);
   }

   getTooltip(card: SpellCard) {
      const card_type = this.game.getCardType(card);
      const { name, cost, description } = card_type;

      const gametext = formatGametext2(_(description));

      let html = `<div class="wg-tooltip-card">
         <div class="wg-tooltip-left">
            <div class="wg-tooltip-header">${_(name)}</div>
            <div class="wg-tooltip-cost">${_("Cost")} : ${cost}</div>
            <div class="wg-tooltip-gametext">${_(gametext)}</div>
         </div>
      </div>`;

      return html;
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

            const growthID = `${this.getId(card)}-growth-id`;
            if (!document.getElementById(growthID)) {
               div.insertAdjacentHTML(
                  "afterbegin",
                  `<div id="${growthID}" class="wg-mana-icon wg-icon-growth">+1</div>`,
               );
            }
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
         cardHeight: card_height,
      });
   }

   getCardById(id: number) {
      return this.getCardStock({ id } as ManaCard)
         .getCards()
         .find((x) => x.id == id);
   }
}

class TooltipManager extends CardManager<SpellCard> {
   constructor(public game: WizardsGrimoire) {
      super(game, {
         getId: (card) => `tooltip-spell-card-${card.id}`,
         setupDiv: (card: SpellCard, div: HTMLElement) => {
            div.classList.add("wg-card");
            div.classList.add("wg-card-spell");
            div.dataset.cardId = "" + card.id;
            div.dataset.type = "" + card.type;
         },
         setupFrontDiv: (card: SpellCard, div: HTMLElement) => {
            div.id = `${this.getId(card)}-front`;
            div.dataset.type = "" + card.type;
            div.classList.add("wg-card-spell-front");

            if (div.childNodes.length == 1 && card.type) {
               const card_type = this.game.getCardType(card);
               const { name, description } = card_type;
               const gametext = formatGametext2(_(description));

               div.insertAdjacentHTML(
                  "afterbegin",
                  `<div class="wg-card-gametext">
                     <div class="wg-card-gametext-title">${_(name)}</div>
                     <div class="wg-card-gametext-divider"></div>
                     <div class="wg-card-gametext-text">${gametext}</div>
                  </div>`,
               );
            }
         },
         setupBackDiv: (card: SpellCard, div: HTMLElement) => {},
         isCardVisible: (card) => true,
         cardWidth: 220,
         cardHeight: 308,
      });
   }

   getCardById(id: number) {
      return this.getCardStock({ id } as SpellCard)
         .getCards()
         .find((x) => x.id == id);
   }
}

class ManaDiscardManager extends CardManager<ManaCard> {
   constructor(public game: WizardsGrimoire) {
      super(game, {
         getId: (card) => `discard-mana-card-${card.id}`,
         setupDiv: (card: ManaCard, div: HTMLElement) => {
            div.classList.add("wg-card");
            div.classList.add("wg-card-mana");
            div.dataset.cardId = "" + card.id;
            div.dataset.type = "" + card.type;
         },
         setupFrontDiv: (card: ManaCard, div: HTMLElement) => {
            div.dataset.type = "" + card.type;
            div.classList.add("wg-card-mana-front");

            const growthID = `${this.getId(card)}-growth-id`;
            if (!document.getElementById(growthID)) {
               div.insertAdjacentHTML(
                  "afterbegin",
                  `<div id="${growthID}" class="wg-mana-icon wg-icon-growth">+1</div>`,
               );
            }
         },
         setupBackDiv: (card: ManaCard, div: HTMLElement) => {},
         isCardVisible: (card) => true,
         cardWidth: 75,
         cardHeight: 105,
      });
   }
}
