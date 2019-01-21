import { uitemplates, replaceSlots, gamedata, groupdata, getGroupData } from './loadgame.js';
export function logoff() {
    location.href = "game/logoff.php";
}
var questionanswered = "<em>Je hebt deze vraag al beantwoord!</em>", reserveterminal = "Om deze vraag te beantwoorden moet je een terminal reserveren. </p><p id='feedbackholder'><button class='input_submit' id='bttn_id'>Reserveer een terminal</button></p>";
var currentquestions;
var currentqgroup;
export function openQGroup() {
    //This is bound to the id of the redirector
    currentqgroup = this;
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
    currentquestions = qs;
    for (var i = 0; i < qs.length; i++) {
        let question = qs[i];
        let answered = false;
        if (groupdata.certificates[this.id]) {
            //Question answered
            let cert = groupdata.certificates[this.id];
            answered = cert.hasOwnProperty(question.id);
        }
        createQuestionSimple(question, answered);
        if (question.useterminal && !answered)
            document.getElementById("request_terminal_" + question.id).addEventListener('click', reserveTerminal.bind(null, question, this));
    }
}
function goBack() {
    //This is bound to the thing to go back to
}
function createQuestionSimple(question, answered) {
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
    let parentholder = raw.getElementsByTagName("div")[0];
    parentholder.id = "question_cont_" + question.id;
    if (!answered) {
        let parentdiv = raw.getElementById("question_");
        parentdiv.appendChild(createQuestionContents(hold, question));
    }
    else {
        hold.innerHTML = questionanswered;
        parentholder.classList.add("obj_certificate-YGOT");
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
    let r = uitemplates.get("questionbody.html");
    let raw = document.implementation.createDocument('http://www.w3.org/1999/xhtml', 'html', null);
    raw.documentElement.appendChild(r.body.cloneNode(true));
    let d = replaceSlots(slots, raw);
    let holderdiv = d.children[0];
    holder.appendChild(holderdiv);
    let inputholder = document.createElement("div");
    inputholder.id = "holder_q_" + question.id;
    if (question.qtype === "text") {
        inputholder.appendChild(raw.getElementById("qanswer_text").cloneNode());
    }
    else if (question.qtype === "radio") {
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
    let feedback = document.createElement("p");
    feedback.className = "feedback";
    feedback.id = "questionfeed_back_" + question.id;
    let a = inputholder.appendChild(raw.getElementById("submit_bttn"));
    let b = inputholder.appendChild(feedback);
    a.addEventListener('click', function () {
        checkAnswer(this);
    }.bind({ q: question, f: feedback }));
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
                setTimeout(() => {
                    getGroupData(false);
                }, question.exptime * 1000);
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
                return true;
            }
        }
    }
    return false;
}
function checkAnswer(data) {
    let question = data.q;
    let holder = document.getElementById("holder_q_" + question.id);
    var qanswer;
    if (question.qtype === "text") {
        qanswer = holder.querySelector(".text-input").value;
    }
    else {
        qanswer = holder.querySelector('.radio-input:checked').value;
    }
    if (question.right_answers.indexOf(qanswer) > -1) {
        data.f.className = "feedback-right";
        data.f.innerHTML = "<i class='material-icons' title='Antwoord goed'>done</i> Je hebt de vraag goed beantwoord!";
        setTimeout(() => {
            submitAnswer(question, qanswer);
            document.getElementById("question_cont_" + question.id).classList.add("obj_certificate-YGOT");
            holder.parentElement.innerHTML = questionanswered;
        }, 5000);
    }
    else {
        data.f.className = "feedback-wrong";
        data.f.innerHTML = "<i class='material-icons' title='Antwoord fout'>warning</i> Oeps, dat was niet goed. Probeer het nog eens.";
    }
}
function submitAnswer(question, answer) {
    var data = {
        game: gamedata.id,
        group: groupdata.id,
        qgroup: currentqgroup.id,
        question: question.id,
        answerdata: {
            correct: true,
            answer: answer,
            points: question.points,
            timeleft: -1
        }
    };
    fetch("game/submitquestion.php", {
        method: "POST",
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
        .then(res => {
        if (res.ok) {
            return res.json();
        }
        else {
            throw new Error();
        }
    })
        .then(json => {
        if (!json.succes || json.succes === 0) {
            alert("Er is iets foutgegaan!");
            throw new Error("Unexpected response from server while submitting: failure to write data, or no data written.");
        }
    });
}
