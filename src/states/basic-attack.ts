class BasicAttackStates implements StateHandler {
   constructor(private game: WizardsGrimoire) {}

   onEnteringState(args: any): void {
      if (!this.game.isCurrentPlayerActive()) return;
      const player_table = this.game.getPlayerTable(this.game.getPlayerId());
      const { hand } = player_table;

      hand.setSelectionMode("single");
      hand.onSelectionChange = (selection: SpellCard[], lastChange: SpellCard) => {
         this.game.toggleButtonEnable("btn_attack", selection && selection.length === 1);
      };
   }

   onLeavingState(): void {
      const { hand } = this.game.getPlayerTable(this.game.getPlayerId());
      hand.setSelectionMode("none");
      hand.onSelectionChange = null;
   }

   onUpdateActionButtons(args: any): void {
      const handleCastSpell = () => {
         const { hand } = this.game.getPlayerTable(this.game.getPlayerId());
         const selectedMana: SpellCard = hand.getSelection()[0];
         this.game.takeAction("basicAttack", { id: selectedMana.id });
      };

      this.game.addActionButtonDisabled("btn_attack", _("Attack"), handleCastSpell);
      this.game.addActionButtonPass();
   }
}
