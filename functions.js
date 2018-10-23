//Global vars
mAnswer = "";
mQuestionData = null;

function toQuestionGroup(qestionGroupID) {
    var URL = "questiongroup.php?qg=" + qestionGroupID;
    window.open(URL, "_self");
}

function makeHexCode() {
    code = Math.random().toString(16).slice(3, 7);
    code = code.toUpperCase();
    return code;
}

//function to replace special chars in strings
function escapeHtml(text) {
    var map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

//function to submit an answer
function submitAnswer(gamePin, qGroup, qNum, groupID, answer, points) {
    // Create our XMLHttpRequest object
    var hr = new XMLHttpRequest();
    // Create some variables we need to send to our PHP file
    var vars = "gamepin=" + gamePin + "&groupid=" + groupID + "&certificate=" + qGroup + "&question=" + qNum + "&answer=" + answer + "&timeleft=" + "na" + "&points=" + points;
    console.log(vars);
    hr.open("POST", "saveanswer.php", true);
    hr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // Access the onreadystatechange event for the XMLHttpRequest object
    hr.onreadystatechange = function() {
            if (hr.readyState == 4 && hr.status == 200) {
                var return_data = hr.responseText;
                document.getElementById("questionfeedback_" + qNum).innerHTML = "<i class='material-icons' title='Antwoord goed & opgeslagen'>done_all</i> Je hebt de vraag goed beantwoord!";
            }
        }
        // Send the data to PHP now... and wait for response to update the status div
    hr.send(vars); // Actually execute the request
}

//function to check an answer
function checkQanswer(gamePin, qGroup, qNum, groupID) {
    //get the questiondata from the JSON-file
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            mQuestionData = JSON.parse(this.responseText);
            //get data from form
            if (mQuestionData.qtype == "text") {
                var qAnswer = escapeHtml(document.getElementById("qanswer_" + qNum).value);
            } else if (mQuestionData.qtype == "radio") {
                var qAnswer = document.querySelector('.radio-input:checked, radio-input[name="qanswer_' + qNum + '"]').value;
            }
            console.log(qAnswer);
            //check if the answer is correct
            if (mQuestionData.ranswers.indexOf(qAnswer.toUpperCase()) >= 0) {
                mAnswer = qAnswer;
                document.getElementById("question_" + qNum).style.display = "none";
                document.getElementById("questionfeedback_" + qNum).setAttribute("class", "feedback-right");
                document.getElementById("questionfeedback_" + qNum).innerHTML = "<i class='material-icons' title='Antwoord goed'>done</i> Je hebt de vraag goed beantwoord!";
                setTimeout(function() { submitAnswer(gamePin, qGroup, qNum, groupID, qAnswer, mQuestionData.points); }, 50)
            } else {
                document.getElementById("questionfeedback_" + qNum).setAttribute("class", "feedback-wrong");
                document.getElementById("questionfeedback_" + qNum).innerHTML = "<i class='material-icons' title='Antwoord fout'>warning</i> Oeps, dat was niet goed. Probeer het nog eens.";
            }
        }
    };
    xmlhttp.open("GET", "games/" + gamePin + "/questions/" + qGroup + "/" + qNum + ".json" + "?" + (new Date()).getTime(), true);
    xmlhttp.send();
}