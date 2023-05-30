interface WizardsGrimoirePlayerData extends BgaPlayer {
   // Add Player data
}

interface WizardsGrimoireGamedatas extends BgaGamedatas<WizardsGrimoirePlayerData> {}

interface Card {
   id: number;
   type: string;
   // type_arg: string;
   location: string;
   location_arg: number;
}

interface SpellCard extends Card {}

interface ManaCard extends Card {}
