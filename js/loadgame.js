//@ts-ignore
import { Spinner } from "https://spin.js.org/spin.js";
import { logoff, openQGroup } from '../js/game.js';
var spinner;
window.onload = function () {
    var target = document.body;
    console.log(Spinner);
    spinner = new Spinner().spin(target);
    initializeCurentParameters();
};
var uifragments = [
    'game_header.html',
    'qgroup.html',
    'question.html',
    'questionbody.html'
];
export var game;
export var group;
export var groupdata, gamedata;
export var questiongroups = [];
var dataready = -1, plusone;
export var uitemplates = new Map();
function initializeCurentParameters() {
    fetch('game/currentsession.php')
        .then(res => { if (res.ok)
        return res.json(); })
        .then(res => {
        if (res.error) {
            displayError("Er is een probleem opgetreden: <i>No session set</i>");
            throw new Error("No session set");
        }
        game = res.game;
        group = res.group;
        document.title = game + ' - ' + group;
        getGameData();
    });
}
export function getGroupData(init) {
    fetch('game/groupdata.php?game=' + game + "&group=" + group)
        .then(res => {
        if (res.ok) {
            return res.json();
        }
    })
        .then(res => {
        if (res.success) {
            groupdata = JSON.parse(res.data);
            if (init)
                initUI(buildUI);
        }
        else {
            alert("Error!");
        }
    });
}
export function getGameData() {
    fetch('game/gamedata.php?game=' + game)
        .then(res => {
        if (res.ok) {
            return res.json();
        }
    })
        .then(res => {
        if (res.success) {
            gamedata = JSON.parse(res.data);
            getGroupData(true);
        }
        else {
            alert("Error!");
        }
    });
}
function initUI(further) {
    if (!(uitemplates.size === 0)) {
        further();
    }
    else {
        let fragments = [];
        for (var fragment of uifragments) {
            fragments.push(fetch('game/' + fragment));
        }
        Promise.all(fragments)
            .then(response => {
            let promises = [];
            response.forEach(res => {
                if (res.ok) {
                    if (res.status !== 200) {
                        displayError("Niet alles kon worden geladen...");
                        throw new Error("Something went wrong");
                    }
                    else {
                        let url = res.url.substr(res.url.lastIndexOf('/') + 1);
                        promises.push(res.text(), url);
                    }
                }
                else {
                    displayError("Niet alles kon worden geladen...");
                    throw new Error("Something went wrong");
                }
            });
            return Promise.all(promises);
        })
            .then(texts => {
            texts.unshift(undefined);
            for (var i = 1; i < texts.length; i += 2) {
                uitemplates.set(texts[i + 1], stringToDom(texts[i]));
            }
            initUI(buildUI);
        });
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
        description.innerHTML = musdecode(qgroup.description);
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
        bttn.title = "Naar vragengroep:" + qgroup.name;
        bttn.addEventListener('click', openQGroup.bind(qgroup));
        let size = raw.getElementById("obj_certificate_banner");
        let ninem = parseFloat(getComputedStyle(size).width);
        let image = new Image();
        image.src = imgurl;
        image.className = "obj_certificate_banner_img";
        size.appendChild(image);
        let d = replaceSlots([name, longname, description], raw);
        for (var e of d.children) {
            holder.appendChild(e);
        }
    }
    setTimeout(() => {
        spinner.stop();
        holder.style.display = "block";
    }, 500);
}
function buildUI() {
    buildHeader();
    buildQuestiongroups();
}
function buildQuestiongroups() {
    if (gamedata.qgroups.length > 0) {
        let promises = [];
        for (var i = 0; i < gamedata.qgroups.length; i++) {
            let url = gamedata.qgroups[i] + "/qgroup.json";
            promises.push(fetch(url));
        }
        Promise.all(promises)
            .then(response => {
            let promises = [];
            response.forEach(res => {
                if (res.ok) {
                    if (res.status !== 200) {
                        displayError("Niet alles kon worden geladen...");
                        throw new Error("Something went wrong");
                    }
                    else {
                        promises.push(res.json());
                    }
                }
                else {
                    displayError("Niet alles kon worden geladen...");
                    throw new Error("Something went wrong");
                }
            });
            return Promise.all(promises);
        })
            .then(json => {
            for (var i = 0; i < json.length; i++) {
                questiongroups.push(json[i]);
            }
            buildQuestiongroupsUI();
        });
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
    console.log(raw.body);
    raw.getElementById("logoffbutton").addEventListener('click', logoff);
    let d = replaceSlots([game, group], raw);
    for (var e of d.children) {
        document.body.prepend(e);
    }
}
export function replaceSlots(replacees, targetdocument) {
    for (var i = 0; i < replacees.length; ++i) {
        let c = replacees[i];
        let id = c.slot;
        let target = targetdocument.getElementById(id);
        if (target) {
            target.parentNode.replaceChild(c, target);
        }
    }
    return targetdocument.body;
}
function displayError(error = "Er is iets foutgegaan") {
    let temp = document.getElementById("error");
    let fragment = temp.content.cloneNode(true);
    document.body.innerHTML = "";
    document.body.appendChild(fragment.getRootNode());
    document.getElementById("e_text").innerHTML = error;
}
function span() {
    return document.createElement("span");
}
function stringToDom(str) {
    var parser = new DOMParser();
    return parser.parseFromString(str, "text/html");
}
