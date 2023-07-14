class Modal {
   public cards: LineStock<SpellCard>;
   private handler;

   constructor(private game: WizardsGrimoire) {
      const html = `<div id="modal-display">
            <i id="modal-display-close" class="fa6 fa6-solid fa6-circle-xmark"></i>
            <div id="modal-display-card"></div>
        </div>`;

      dojo.place(html, "ebd-body", "last");

      this.cards = new LineStock<SpellCard>(
         game.tooltipManager,
         document.getElementById("modal-display-card"),
      );

      const handleKeyboard = (ev: KeyboardEvent) => {
         if (document.getElementById("ebd-body").classList.contains("modal_open")) {
            if (ev.key == "Escape") {
               this.close();
            }
         }
      };

      document.getElementById("modal-display").addEventListener("click", () => this.close());
      document.getElementById("modal-display-close").addEventListener("click", () => this.close());
      document.getElementById("ebd-body").addEventListener("keydown", handleKeyboard);
   }

   display(card: SpellCard) {
      this.cards.removeAll();
      this.cards.addCard(card);
      const scrollY = window.scrollY;

      const body = document.getElementById("ebd-body");
      body.classList.toggle("modal_open", true);
      body.style.top = `-${scrollY}px`;

      const display = document.getElementById("modal-display");
      display.style.top = `${scrollY}px`;
   }

   close() {
      const body = document.getElementById("ebd-body");
      body.classList.toggle("modal_open", false);
      body.style.top = ``;

      const display = document.getElementById("modal-display");
      const scrollY = Number(display.style.top.replace("px", ""));
      display.style.top = `${scrollY}px`;

      window.scroll(0, scrollY);

      this.cards.removeAll();
   }
}
