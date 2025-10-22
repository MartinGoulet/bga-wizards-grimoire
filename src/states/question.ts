class QuestionStates implements StateHandler {
   constructor(private game: Game) {}
   onEnteringState(args: QuestionArgs): void {}
   onLeavingState(): void {}
   onUpdateActionButtons(args: QuestionArgs): void {
      const { options, cancel } = args;
      let index = 0;
      options?.forEach(({ label, action, color }) => {
         this.game.statusBar.addActionButton(label, action, {
            id: `btn_action_${index++}`,
            color
         });
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
      color?: "primary" | "secondary" | "alert";
   }[];
   cancel: boolean;
}
