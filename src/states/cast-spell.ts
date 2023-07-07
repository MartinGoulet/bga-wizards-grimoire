class CastSpellStates implements StateHandler {
   constructor(private game: WizardsGrimoire) {}

   onEnteringState(args: CastSpellArgs): void {
      this.game.clearSelection();
      if (!this.game.isCurrentPlayerActive()) return;
      const player_table = this.game.getCurrentPlayerTable();
      const repertoire = player_table.spell_repertoire;

      player_table.setDiscountNextAttack(args.discount_attack_spell);
      player_table.setDiscountNextSpell(args.discount_next_spell);

      const selectableCards = repertoire
         .getCards()
         .filter(
            (card) =>
               player_table.canCast(card) &&
               player_table.mana_cooldown[card.location_arg].getCardNumber() == 0,
         );

      repertoire.setSelectionMode("single");
      repertoire.setSelectableCards(selectableCards);
      repertoire.onSelectionChange = (selection: SpellCard[], lastChange: SpellCard) => {
         this.game.toggleButtonEnable("btn_cast", selection && selection.length === 1);
      };
   }

   onLeavingState(): void {
      const repertoire = this.game.getCurrentPlayerTable().spell_repertoire;
      repertoire.setSelectionMode("none");
      repertoire.onSelectionChange = null;
   }

   onUpdateActionButtons(args: CastSpellArgs): void {
      const handleCastSpell = () => {
         const repertoire = this.game.getCurrentPlayerTable().spell_repertoire;
         const selectedSpell: SpellCard = repertoire.getSelection()[0];

         this.game.markCardAsSelected(selectedSpell);
         this.game.actionManager.setup("castSpell", "actionCastMana");
         this.game.actionManager.addAction(selectedSpell);
         this.game.actionManager.activateNextAction();
      };

      const handlePass = () => {
         this.game.takeAction("pass");
      };

      if (this.hasSpellAvailable()) {
         this.game.addActionButtonDisabled("btn_cast", _("Cast spell"), handleCastSpell);
         this.game.addActionButtonRed("btn_pass", _("Move to basic attack"), handlePass);
      } else {
         this.game.addActionButton("btn_pass", _("Move to basic attack"), handlePass);
      }
   }

   restoreGameState() {
      return new Promise<boolean>((resolve) => resolve(true));
   }

   hasSpellAvailable() {
      const nbr_empty_deck = this.game
         .getPlayerTable(this.game.getPlayerId())
         .getManaDeckWithSpellOver()
         .filter((deck) => deck.isEmpty()).length;

      return nbr_empty_deck > 0;
   }
}

interface CastSpellArgs {
   discount_attack_spell: number;
   discount_next_spell: number;
}
