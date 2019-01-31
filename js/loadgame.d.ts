export interface GameData {
    creator: string;
    id: string;
    grouptitle: string;
    image: string;
    imagelocation: string;
    maxterminals: number;
    creationtime: number;
    qgroups: Array<string>;
    terminals : Array<any>
}
export interface QuestionGroup {
    name: string;
    longname: string;
    id: string;
    description: string;
    image: string;
    imagelocation: string;
    questions: Array<Question>;
    imgurl?: string;
}
export interface Question {
    id: string;
    title: string;
    description: string;
    qtype: string;
    q_pswd: string;
    image: string;
    question: string;
    answers: Array<string>;
    right_answers: Array<string>;
    points: number;
    exptime: number;
    useterminal: boolean;
    terminals: Array<any>;
}
export declare var game: any;
export declare var group: any;
export declare var groupdata: any, gamedata: GameData;
export declare var questiongroups: Array<QuestionGroup>;
export declare var uitemplates: Map<string, Document>;
export declare function replaceSlots(replacees: Array<Element>, targetdocument: Document): HTMLBodyElement;
