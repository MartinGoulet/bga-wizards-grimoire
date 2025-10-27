class CastSpellStates implements StateHandler {
   constructor(private game: Game) {}

   onEnteringState(args: CastSpellArgs): void {
      log("Entering CastSpell state", args);
      this.game.clearSelection();
      if (!this.game.isCurrentPlayerActive()) return;
      const player_table = this.game.getCurrentPlayerTable();
      const repertoire = player_table.spell_repertoire;

      player_table.setDiscountNextAttack(args.discount_attack_spell);
      player_table.setDiscountNextSpell(args.discount_next_spell);
      player_table.setPreviousSpellPlayed(args.previous_spell_played);
      player_table.setPreviousSpellCost(args.previous_spell_cost);
      player_table.setCursedMindIncreaseCost(args.cursed_mind);
      player_table.setCrescendoIncreaseCost(args.crescendo);
      player_table.setPremonitionDiscount(args['premonition_discount']);
      player_table.spell_discount = args.spell_discount;

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
         if (selection && selection.length === 1) {
            setTimeout(() => {
               const repertoire = this.game.getCurrentPlayerTable().spell_repertoire;
               const selectedSpell: SpellCard = repertoire.getSelection()[0];
               this.game.markCardAsSelected(selectedSpell);
               this.game.actionManager.setup("actCastSpell", "actionCastMana");
               this.game.actionManager.addAction(selectedSpell);
               this.game.actionManager.activateNextAction();
            }, 10);
         }
      };
   }

   onLeavingState(): void {
      const repertoire = this.game.getCurrentPlayerTable().spell_repertoire;
      repertoire.setSelectionMode("none");
      repertoire.onSelectionChange = null;
   }

   onUpdateActionButtons(args: CastSpellArgs): void {
      const handleCastSpell = async () => {
         const selectedSpell = this.game.getCurrentPlayerTable().spell_repertoire.getSelection()[0];
         if (selectedSpell != null) {
            await this.game.bgaPerformAction("actCastSpell", { id: selectedSpell.id });
         }
      };

      const handlePass = async () => {
         await this.game.bgaPerformAction("actPass");
      };

      if (this.hasSpellAvailable()) {
         this.game.addActionButtonDisabled("btn_cast", _("Cast spell"), handleCastSpell);
         this.game.addActionButtonRed("btn_pass", _("Move to basic attack"), handlePass);
      } else {
         this.game.addActionButton("btn_pass", _("Move to basic attack"), handlePass);
      }

      if (args.undo) {
         this.game.addActionButtonUndo();
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
   previous_spell_played: number;
   previous_spell_cost: number;
   cursed_mind: number;
   crescendo: number;
   undo: boolean;
   spell_discount: Record<number, number>;
}
