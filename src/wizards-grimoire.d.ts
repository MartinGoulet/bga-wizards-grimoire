interface WizardsGrimoirePlayerData extends BgaPlayer {
   // Add Player data
}

type PlayerBoardObject = { [player_id: number]: PlayerBoardInfo };

interface WizardsGrimoireGamedatas extends BgaGamedatas<WizardsGrimoirePlayerData> {
   slot_count: 8 | 10;
   slot_cards: SpellCard[];

   spells: SpellInfo;
   manas: ManaInfo;

   card_types: { [card_type: number]: CardType };
   player_board: PlayerBoardObject;
}

interface PlayerBoardInfo {
   manas: { [position: number]: ManaCard[] };
   spells: SpellCard[];
   hand?: ManaCard[];
}

interface Card {
   id: number;
   type: string;
   // type_arg: string;
   location: string;
   location_arg: number;
}

interface CardType {
   name: string;
   description: string;
   type: "red" | "green" | "purple";
   activation: "instant" | "delayed" | "ongoing";
   cost: number;
   icon: "+" | "++" | "scroll";
   js_actions?: string[] | string;
}

interface SpellCard extends Card {
   isHidden?: boolean;
}

interface ManaCard extends Card {
   isHidden?: boolean;
}

interface DeckInfo {
   deck_count: number;
   discard_count: number;
}
interface SpellInfo extends DeckInfo {}
interface ManaInfo extends DeckInfo {}

/////////////////////////////////////////////
// States
interface StateHandler {
   onEnteringState(args: any): void;
   onLeavingState(): void;
   onUpdateActionButtons(args: any): void;
}

/**
 * Notifications
 */

interface NotifChooseSpellArgs {
   player_id: number;
   card: SpellCard;
}

interface NotifRefillSpellArgs {
   card: SpellCard;
}

interface NotifDrawManaCardsArgs {
   player_id: number;
   cards: ManaCard[];
}

interface NotifSpellCoolDownArgs {
   player_id: number;
   mana_cards_discard: { pos: number | ManaCard };
}

interface NotifHealthChangedArgs {
   player_id: number;
   life_remaining: number;
   damage: number;
}
