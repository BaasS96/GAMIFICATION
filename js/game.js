import { uitemplates, replaceSlots, questiongroups, gamedata, groupdata } from './loadgame.js';
export function logoff() {
    location.href = "game/logoff.php";
}
var questionanswered = "<em>Je hebt deze vraag al beantwoord!</em>", reserveterminal = "Om deze vraag te beantwoorden moet je een terminal reserveren. </p><p id='feedbackholder'><button class='input_submit' onclick=''>Reserveer een terminal</button></p>";
export function openQGroup() {
    //This is bound to the id of the redirector
    var holder = document.getElementById("holder");
    let frag = createBreadCrumb('Game ' + gamedata.id + ' - Vragengroup ' + this, goBack, 'game');
    holder.innerHTML = "";
    holder.appendChild(frag);
    //Create questions
    let questions = questiongroups[0].questions;
    var question;
    if (typeof questions === "object") {
        question = Object.keys(questions);
    }
    createQuestionSimple(questions[question[0]], questiongroups[0]);
}
function goBack() {
    //This is bound to the thing to go back to
}
function createQuestionSimple(question, questiongroup) {
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
    let raw = uitemplates.get("question.html");
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
        parentdiv.querySelector("button .input_submit").addEventListener('click', reserveTerminal.bind(null, question, questiongroup));
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
        holder.innerHTML = reserveterminal;
        return document.createElement("div");
    }
    else {
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
    }
    else if (question.qtype === "radio") {
        for (var i = 0; i < question.answers.length; i++) {
            let answerlabel = raw.getElementById("q_label").cloneNode();
            answerlabel.htmlFor = "q_" + i.toString();
            answerlabel.id = "q_label_" + i.toString();
            let radio = raw.getElementById("qanswer_mc");
            radio.name = "q_" + i.toString();
            radio.id = "q_" + i.toString();
            inputholder.appendChild(radio);
            inputholder.appendChild(answerlabel);
            inputholder.innerHTML += "<br>";
        }
    }
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
    let query = "reserveterminal.php?";
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
    }
    else {
        initial.fetchpromise
            .then(res => {
            if (res.ok) {
                return res.json();
            }
        })
            .then(res => {
            if (res.success) {
                alert(res.terminal);
            }
            else {
                alert(res.error);
            }
        });
    }
}
function groupIsAlreadyUsingTerminal(inuse, assignable) {
    for (var i = 0; i < inuse.length; i++) {
        for (var j = 0; j < assignable.length; j++) {
            if (inuse[i] === assignable[j]) {
                return false;
            }
        }
    }
    return true;
}
