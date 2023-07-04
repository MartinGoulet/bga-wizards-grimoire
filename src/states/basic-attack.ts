class BasicAttackStates implements StateHandler {
   constructor(private game: WizardsGrimoire) {}

   onEnteringState(args: BasicAttackStates): void {
      if (!this.game.isCurrentPlayerActive()) return;
      const player_table = this.game.getCurrentPlayerTable();
      const { hand } = player_table;

      hand.setSelectionMode("single");
      hand.setSelectableCards(args._private.allowed_manas);

      hand.onSelectionChange = (selection: SpellCard[], lastChange: SpellCard) => {
         this.game.toggleButtonEnable("btn_attack", selection && selection.length === 1);
      };
   }

   onLeavingState(): void {
      const { hand } = this.game.getCurrentPlayerTable();
      hand.setSelectionMode("none");
      hand.onSelectionChange = null;
   }

   onUpdateActionButtons(args: BasicAttackStates): void {
      const handleCastSpell = () => {
         const { hand } = this.game.getCurrentPlayerTable();
         const selectedMana: ManaCard = hand.getSelection()[0];
         if (selectedMana) {
            this.game.takeAction("basicAttack", { id: selectedMana.id });
         }
      };

      this.game.addActionButtonDisabled("btn_attack", _("Attack"), handleCastSpell);
      this.game.addActionButtonPass();
   }

   restoreGameState() {
      return new Promise<boolean>((resolve) => resolve(true));
   }
}

interface BasicAttackStates {
   _private: {
      allowed_manas: ManaCard[];
   };
}
