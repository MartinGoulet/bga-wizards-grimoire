class BasicAttackStates implements StateHandler {
   constructor(private game: WizardsGrimoire) {}

   onEnteringState(args: BasicAttackStates): void {
      if (!this.game.isCurrentPlayerActive()) return;
      const player_table = this.game.getPlayerTable(this.game.getPlayerId());
      const { hand } = player_table;

      hand.setSelectionMode("single");
      debugger;
      hand.setSelectableCards(args.allowed_manas);

      hand.onSelectionChange = (selection: SpellCard[], lastChange: SpellCard) => {
         this.game.toggleButtonEnable("btn_attack", selection && selection.length === 1);
      };
   }

   onLeavingState(): void {
      const { hand } = this.game.getPlayerTable(this.game.getPlayerId());
      hand.setSelectionMode("none");
      hand.onSelectionChange = null;
   }

   onUpdateActionButtons(args: BasicAttackStates): void {
      const handleCastSpell = () => {
         const { hand } = this.game.getPlayerTable(this.game.getPlayerId());
         const selectedMana: SpellCard = hand.getSelection()[0];
         if (selectedMana) {
            this.game.takeAction("basicAttack", { id: selectedMana.id });
         }
      };

      this.game.addActionButtonDisabled("btn_attack", _("Attack"), handleCastSpell);
      this.game.addActionButtonPass();
   }

   restoreGameState() {}
}

interface BasicAttackStates {
   allowed_manas: ManaCard[];
}
