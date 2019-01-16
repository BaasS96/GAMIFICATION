import { uitemplates, replaceSlots, gamedata, groupdata, getGroupData, getGameData } from './loadgame.js';
export function logoff() {
    location.href = "game/logoff.php";
}
var questionanswered = "<em>Je hebt deze vraag al beantwoord!</em>",
    reserveterminal = "Om deze vraag te beantwoorden moet je een terminal reserveren. </p><p id='feedbackholder'><button class='input_submit' id='bttn_id'>Reserveer een terminal</button></p>";
export function openQGroup() {
    //This is bound to the id of the redirector
    var holder = document.getElementById("holder");
    let frag = createBreadCrumb('Game ' + gamedata.id + ' - Vragengroup ' + this.id, goBack, 'game');
    holder.innerHTML = "";
    holder.appendChild(frag);
    var qs = this.questions;
    if (typeof qs === "object") {
        //@ts-ignore
        let temp = Object.entries(this.questions);
        qs = [];
        for (var questiontemp of temp) {
            qs.push(questiontemp[1]);
        }
    }
    for (var i = 0; i < qs.length; i++) {
        let question = qs[i];
        createQuestionSimple(question);
        if (question.useterminal)
            document.getElementById("request_terminal_" + question.id).addEventListener('click', reserveTerminal.bind(null, question, this));
    }
}

function goBack() {
    //This is bound to the thing to go back to
}

function createQuestionSimple(question) {
    var title = document.createElement("h1");
    title.slot = "question_title";
    title.innerHTML = question.title;
    var description = document.createElement("p");
    description.slot = "question_description";
    description.innerHTML = question.description;
    var feedback = document.createElement("p");
    feedback.slot = "feedback";
    feedback.innerHTML = "GOOD";
    feedback.className = "feedback";
    var hold = document.createElement("p");
    hold.slot = "question_body";
    hold.className = "question_divided";
    let r = uitemplates.get("question.html");
    let raw = document.implementation.createDocument('http://www.w3.org/1999/xhtml', 'html', null);
    raw.documentElement.appendChild(r.body.cloneNode(true));
    let answered = false;
    if (groupdata.certificates[this]) {
        //Question answered
        if (groupdata.certificates[this][question.id]) {
            answered = true;
            hold.innerHTML = questionanswered;
        }
    }
    if (!answered) {
        let parentdiv = raw.getElementById("question_");
        parentdiv.appendChild(createQuestionContents(hold, question));
    }
    let d = replaceSlots([title, description, feedback, hold], raw);
    var holder = document.getElementById("holder");
    for (var e of d.children) {
        holder.appendChild(e);
    }
}

function createQuestionContents(holder, question) {
    let q = document.createElement("em");
    q.innerHTML = question.question;
    let slots = [];
    if (question.useterminal) {
        holder.innerHTML = reserveterminal.replace("bttn_id", "request_terminal_" + question.id);
        return document.createElement("div");
    } else {
        if (question.image) {
            let image = document.createElement("img");
            image.className = "question_image";
            image.slot = "question_image";
            image.src = question.image;
            slots.push(image);
        }
    }
    let raw = uitemplates.get("questionbody.html");
    let d = replaceSlots(slots, raw);
    let holderdiv = d.children[0];
    holder.appendChild(holderdiv);
    let inputholder = document.createElement("div");
    if (question.qtype === "text") {
        inputholder.appendChild(raw.getElementById("qanswer_text"));
    } else if (question.qtype === "radio") {
        for (var i = 0; i < question.answers.length; i++) {
            let answerlabel = raw.getElementById("q_label").cloneNode();
            answerlabel.htmlFor = "q_" + i.toString();
            answerlabel.id = "q_label_" + i.toString();
            answerlabel.innerHTML = question.answers[i];
            let radio = raw.getElementById("qanswer_mc").cloneNode();
            radio.name = "q_" + question.question;
            radio.id = "q_" + i.toString();
            inputholder.appendChild(radio);
            inputholder.appendChild(answerlabel);
            inputholder.innerHTML += "<br>";
        }
    }
    let submit = raw.getElementById("submit_bttn").cloneNode(true);
    inputholder.appendChild(submit);
    return inputholder;
}

function createBreadCrumb(text, action, actionarg) {
    let crumbtemplate = document.getElementById("breadcrumb");
    let docfrag = crumbtemplate.content.cloneNode(true);
    docfrag.getElementById("text_bread").innerHTML = text;
    docfrag.getRootNode().addEventListener('click', action.bind(actionarg));
    return docfrag.getRootNode();
}

function requestTerminal(questionGroup, question) {
    let query = "game/reserveterminal.php?";
    query += "gameid=" + gamedata.id;
    query += "&groupid=" + groupdata.id;
    query += "&qgroupid=" + questionGroup.id;
    query += "&questionid=" + question.id;
    query += "&validterminals=" + question.terminals.join(",");
    if (groupdata.terminals.length == gamedata.maxterminals) {
        return { success: false, resulttext: "Deze groep kan niet meer terminals aanvragen" };
    }
    if (groupIsAlreadyUsingTerminal(groupdata.terminals, question.terminals)) {
        return { success: false, resulttext: "Deze groep heeft al een terminal voor deze vraag in gebruik" };
    }
    return { fetchpromise: fetch(query) };
}

function reserveTerminal(question, questiongroup) {
    let initial = requestTerminal(questiongroup, question);
    if (initial.success !== undefined) {
        alert(initial.resulttext);
    } else {
        initial.fetchpromise
            .then(res => {
                if (res.ok) {
                    return res.json();
                }
            })
            .then(res => {
                if (res.success) {
                    getGroupData();
                    getGameData();
                    alert(res.terminal);
                } else {
                    alert(res.error);
                }
            });
    }
}

function groupIsAlreadyUsingTerminal(inuse, assignable) {
    for (var i = 0; i < inuse.length; i++) {
        for (var j = 0; j < assignable.length; j++) {
            if (inuse[i] === assignable[j]) {
                return true;
            }
        }
    }
    return false;
}