class QuestionStates implements StateHandler {
   constructor(private game: WizardsGrimoire) {}
   onEnteringState(args: QuestionArgs): void {}
   onLeavingState(): void {}
   onUpdateActionButtons(args: QuestionArgs): void {
      const { options, cancel } = args;
      let index = 0;
      options?.forEach(({ label, action, color }) => {
         this.game.addActionButton(`btn_action_${index++}`, label, action, null, null, color);
      });
      if (cancel) {
         this.game.addActionButtonClientCancel();
      }
   }
   restoreGameState() {
      return new Promise<boolean>((resolve) => resolve(true));
   }
}

interface QuestionArgs {
   options: {
      label: string;
      action: () => void;
      color?: BgaButtonColor;
   }[];
   cancel: boolean;
}
