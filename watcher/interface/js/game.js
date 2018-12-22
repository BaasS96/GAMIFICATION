export class Game {
    constructor(data) {
        this.data = data;
        //@ts-ignore
        //delete this.data.terminals;
        //delete this.data.qgroups;
        let date = new Date(this.data.creationtime * 1000);
        this.data.creationtime = (date.getDate() + "-" + date.getMonth() + "-" + date.getFullYear());
        this.lastupdate = Math.round((new Date()).getTime() / 1000);
    }
    update(newdata) {
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
        l += "</div></div>";
        return l;
    }
}
