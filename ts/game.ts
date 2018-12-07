import {Question, uitemplates, replaceSlots, questiongroups, gamedata, groupdata, QuestionGroup, getGroupData, getGameData} from './loadgame.js';

export function logoff() {
    location.href = "game/logoff.php";
}

var questionanswered = "<em>Je hebt deze vraag al beantwoord!</em>",
    reserveterminal = "Om deze vraag te beantwoorden moet je een terminal reserveren. </p><p id='feedbackholder'><button class='input_submit' id='bttn_id'>Reserveer een terminal</button></p>";

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
    document.getElementById("request_terminal_" + questions[question[0]].id).addEventListener('click', reserveTerminal.bind(null, questions[question[0]], questiongroups[0]));
}

function goBack() {
    //This is bound to the thing to go back to
}

function createQuestionSimple(question : Question, questiongroup : QuestionGroup) {
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
    }

    let d = replaceSlots([title, description, feedback, hold], raw);
    var holder = document.getElementById("holder");
    for (var e of d.children) {
        holder.appendChild(e);
    }
}

function createQuestionContents(holder : HTMLElement, question : Question) : HTMLDivElement {
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
            let answerlabel = <HTMLLabelElement>raw.getElementById("q_label").cloneNode();
            answerlabel.htmlFor = "q_" + i.toString();
            answerlabel.id = "q_label_" + i.toString();

            let radio = <HTMLInputElement>raw.getElementById("qanswer_mc");
            radio.name = "q_" + i.toString();
            radio.id = "q_" + i.toString();

            inputholder.appendChild(radio);
            inputholder.appendChild(answerlabel);
            inputholder.innerHTML += "<br>";
        }
    }

    return inputholder;
}

function createBreadCrumb(text : string, action : Function, actionarg : any) {
    let crumbtemplate : HTMLTemplateElement = <HTMLTemplateElement>document.getElementById("breadcrumb");
    let docfrag : DocumentFragment = <DocumentFragment>crumbtemplate.content.cloneNode(true);
    docfrag.getElementById("text_bread").innerHTML = text;
    docfrag.getRootNode().addEventListener('click', action.bind(actionarg));
    return docfrag.getRootNode();
}

interface TerminalRequestResult {
    success? : boolean;
    resulttext? : string;
    fetchpromise? : Promise<Response>;
}

function requestTerminal(questionGroup : QuestionGroup, question : Question) : TerminalRequestResult {
    let query = "game/reserveterminal.php?";
    query += "gameid=" + gamedata.id;
    query += "&groupid=" + groupdata.id;
    query += "&qgroupid=" + questionGroup.id;
    query += "&questionid=" + question.id;
    query += "&validterminals=" + question.terminals.join(",");
    
    if (groupdata.terminals.length == gamedata.maxterminals) {
        return {success: false, resulttext: "Deze groep kan niet meer terminals aanvragen"};
    }
    
    if (groupIsAlreadyUsingTerminal(groupdata.terminals, question.terminals)) {
        return {success: false, resulttext: "Deze groep heeft al een terminal voor deze vraag in gebruik"};
    }

    return {fetchpromise: fetch(query)};
}

function reserveTerminal(question : Question, questiongroup : QuestionGroup) {
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

function groupIsAlreadyUsingTerminal(inuse : Array<string>, assignable : Array<string>) {
    for (var i = 0; i < inuse.length; i++) {
        for (var j = 0; j < assignable.length; j++) {
            if (inuse[i] === assignable[j]) {
                return true;
            }
        }
    }
    return false;
}