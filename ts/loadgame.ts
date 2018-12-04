//@ts-ignore
import { Spinner } from "https://spin.js.org/spin.js";

var spinner;

window.onload = function () {
    var target = document.body;
    console.log(Spinner);
    spinner = new Spinner().spin(target);
    initializeCurentParameters();
    waitForFetch();
};

var uifragments = [
    'game_header.html',
    'qgroup.html'
];

interface GameData {
    creator : string,
    id : string,
    grouptitle : string,
    image : string,
    imagelocation : string,
    maxterminals : number,
    creationtime : number,
    qgroups : Array<string>
}

interface QuestionGroup {
    name : string,
    longname : string,
    id : string,
    description : string,
    image : string,
    imagelocation : string,
    questions : Array<Question>
    imgurl? : string
}

interface Question {

}

var game;
var group;
var groupdata, gamedata : GameData;
var questiongroups : Array<QuestionGroup> = [];
var dataready = -1, plusone;
var uitemplates = new Map<string, Document>();

function initializeCurentParameters() {
    fetch('game/currentsession.php')
        .then(res => { if (res.ok) return res.json() })
        .then(res => { game = res.game; group = res.group; document.title = game + ' - ' + group; getGameData(), getGroupData(); });
}

function getGroupData() {
    fetch('game/groupdata.php?game=' + game + "&group=" + group)
        .then(res => {
            if (res.ok) {
                return res.json();
            }
        })
        .then(res => {
            if (res.success) {
                if (!dataready) {
                    dataready++;
                }
                groupdata = JSON.parse(res.data);
            } else {
                alert("Error!");
            }
        })
}

function getGameData() {
    fetch('game/gamedata.php?game=' + game)
        .then(res => {
            if (res.ok) {
                return res.json();
            }
        })
        .then(res => {
            if (res.success) {
                dataready++;
                gamedata = JSON.parse(res.data);
            } else {
                alert("Error!");
            }
        })
}

function waitForFetch() {
    if (dataready < 1) {
        setTimeout(waitForFetch, 10);
    } else {
        initUI(buildUI);
    }
}

function waitForFetch_2() {
    if (dataready < 1) {
        setTimeout(waitForFetch_2, 10);
    } else {
        buildQuestiongroupsUI();
    }
}

function initUI(further : Function) {
    if (!(uitemplates.size === 0)) {
        further();
    } else {
        dataready = -1;
        plusone = Math.ceil(2 / uifragments.length);
        uifragments.forEach(i =>  {
            fetch('game/' + i)
                .then(res => {
                    if (res.ok) {
                        if (res.status !== 200) {
                            dataready = -1;
                            displayError("Niet alles kon worden geladen...");
                            throw new Error("Something went wrong");
                        } else {
                            return res.text();
                        }
                    } else {
                        dataready = -1;
                        displayError("Niet alles kon worden geladen...");
                        throw new Error("Something went wrong");
                    }
                })
                .then(res => {
                    dataready += plusone;
                    uitemplates.set(i, stringToDom(res));
                });
        });
        waitForFetch();
    }
}

function buildQuestiongroupsUI() {
    let holder = document.getElementById("holder");
    for (var i = 0; i < questiongroups.length; i++) {
        let qgroup = questiongroups[i];
            let name = document.createElement("div");
            name.className = "obj_certificate_name";
            name.slot = "obj_certificate_name";
            name.innerHTML = qgroup.name;

            let longname = document.createElement("h1");
            longname.slot = "longname";
            longname.innerHTML = qgroup.longname;
            
            let description = document.createElement("p");
            description.slot = "description";
            description.innerHTML = qgroup.description;

            let imgurl = qgroup.image;
            if (qgroup.imagelocation === "main") {
                imgurl = "images/" + imgurl;
            }
            else if (qgroup.imagelocation === "game") {
                imgurl = "games/" + gamedata.id + "/images/" + imgurl;
            }
            qgroup.imgurl = imgurl;
            
            let r = uitemplates.get('qgroup.html');
            let raw = document.implementation.createDocument('http://www.w3.org/1999/xhtml', 'html', null);
            raw.documentElement.appendChild(r.body.cloneNode(true));
            let bttn = raw.getElementById("obj_certificate");
            bttn.title = "Naar vragengroep:"  + qgroup.name;
            bttn.addEventListener('click', openQGroup.bind(qgroup.id));
            raw.getElementById("obj_certificate_banner").style.backgroundImage = "url(" + imgurl + ")";
            let d = replaceSlots([name, longname, description], raw);
            for (var e of d.children) {
                holder.appendChild(e);
            }
    }
    setTimeout(() => {
        spinner.stop();
        holder.style.display = "block";
    },1000);
}

function buildUI() {
    buildHeader();
    buildQuestiongroups();
}

function buildQuestiongroups() {
    if (gamedata.qgroups.length > 0) {
        dataready = -1; 
        plusone = Math.ceil(2 / gamedata.qgroups.length);
        for (var i = 0; i < gamedata.qgroups.length; i++) {
            let url = gamedata.qgroups[i] + "/qgroup.json";
            fetch(url)
            .then(res => {
                if (res.ok) {
                    if (res.status !== 200) {
                        dataready = -1;
                        displayError("Niet alles kon worden geladen...");
                        throw new Error("Something went wrong");
                    } else {
                        return res.json();
                    }
                } else {
                    dataready = -1;
                    displayError("Niet alles kon worden geladen...");
                    throw new Error("Something went wrong");
                }
            })
            .then(res => {
                dataready += plusone;
                questiongroups.push(<QuestionGroup><unknown>res);
            });
        }
        waitForFetch_2();
    }
}

function buildHeader() {
    let game = span();
    game.slot = "top_banner_res";
    game.className = "top_banner_res";
    game.innerHTML = gamedata.grouptitle;
    
    let group = span();
    group.slot = "top_banner_resname";
    group.className = "top_banner_resname";
    group.innerHTML = groupdata.name;

    let raw = uitemplates.get('game_header.html');
    raw.getElementById("logoffbutton").addEventListener('click', logoff);
    let d = replaceSlots([game, group], raw);
    for (var e of d.children) {
        document.body.prepend(e);
    }
}

function replaceSlots(replacees : Array<Element>, targetdocument : Document) : HTMLBodyElement{
    for (var i = 0; i < replacees.length; ++i) {
        let c = replacees[i];
        let id = c.slot;
        let target = targetdocument.getElementById(id);
        if (target) {
            target.parentNode.replaceChild(c, target); 
        }
    }
    return <HTMLBodyElement>targetdocument.body;
}

function displayError(error : string) {
    let temp = <HTMLTemplateElement>document.getElementById("error");
    let fragment = temp.content.cloneNode(true);
    document.body.innerHTML = "";
    document.body.appendChild(fragment.getRootNode());
    document.getElementById("e_text").innerHTML = error;
}

function span() {
    return document.createElement("span");
}

function stringToDom(str) {
    var parser = new DOMParser()
    return parser.parseFromString(str, "text/html");
}