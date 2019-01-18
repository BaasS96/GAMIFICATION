import {GameData} from '../../../js/loadgame';

export class Game {
    lastupdate: number;
    data: GameData;

    element: HTMLElement;

    terminals : Object;
    qgroups : any;

    constructor(data: GameData) {
        this.data = data;
        //@ts-ignore
        this.terminals = this.data.terminals;
        delete this.data.terminals;
        this.qgroups = this.data.qgroups;
        delete this.data.qgroups;

        let imgurl = this.data.image;
        if (this.data.imagelocation === "main") {
            imgurl = "../../images/" + imgurl;
        }
        else if (this.data.imagelocation === "game") {
            imgurl = "../../games/" + this.data.id + "/images/" + imgurl;
        }
        this.data.image = imgurl;

        let date = new Date(this.data.creationtime * 1000);
        this.data.creationtime = <number><unknown>(date.getDate() + "-" + date.getMonth() + "-" + date.getFullYear());
        this.lastupdate = Math.round((new Date()).getTime() / 1000);
    }

    get getTerminals() {
        return this.terminals;
    }

    get getQGroups() {
        return this.qgroups;
    }

    update(newdata: GameData) {
        this.data = newdata;
        this.lastupdate = Math.round((new Date()).getTime() / 1000);
    }   

    render() {
        let l = `<div class="gameholder">
                <div class="holder_labels">`;
        let spans = [];         
        for (var prop in this.data) {
            l += "<span>" + prop + "</span>";
            spans.push("<span>" + this.data[prop] + "</span>");
        }
        l += `</div><div class="holder_info">`;
        for (var p of spans) {
            l += p;
        }   
        l += `</div>
        </div>
        <div class="titlebar sub">
        Image
        </div>
        <img class="game_image" src="` + this.data.image + `"\\>`;
        return l;
    }
}