window.onload = function () {
    initializeTerminal();
    document.getElementsByClassName("holder-v")[0].addEventListener('click', toggleLockState);
};
var game;
var terminal;
var params;
var terminaldata, questiondata;
var inuse = false, locked = true;
var timeremaining, countdown, countdownrunning = false;
var givenanswer, answerright;
function initializeTerminal() {
    params = window.location.search.substr(1);
    let looseparams = params.split("&");
    game = looseparams[0].split("=")[1];
    terminal = looseparams[1].split("=")[1];
    fetch("terminaldata.php?" + params)
        .then(res => {
        if (res.ok) {
            return res.json();
        }
    })
        .then(res => {
        if (res.success) {
            terminaldata = res.data;
            document.getElementById("contentHolder").innerHTML = "<p class='idletext'>" + terminaldata.text + "</p>";
        }
        else {
            alert("Error");
        }
    });
    setInterval(() => {
        if (!inuse) {
            poll(false);
        }
    }, 1000);
}
function poll(fetched) {
    if (!fetched) {
        fetch("terminaldata.php?" + params)
            .then(res => {
            if (res.ok) {
                return res.json();
            }
        })
            .then(res => {
            if (res.success) {
                terminaldata = res.data;
                poll(true);
            }
            else {
                alert("Error");
            }
        });
    }
    else {
        if (terminaldata.inuse && terminaldata.inuse !== inuse) {
            //getQuestionData();
            inuse = true;
            lockTerminal();
            countdown = setInterval(countDown, 1000);
            getQuestionData();
        }
    }
}
function getQuestionData() {
    questiondata = undefined;
    fetch('questiondata.php?game=' + game + "&qgroup=" + terminaldata.questiongroup + "&q=" + terminaldata.question)
        .then(res => {
        if (res.ok) {
            return res.json();
        }
    })
        .then(res => {
        if (res !== null) {
            timeremaining = parseInt(res.exptime) * 1000;
            countdownrunning = true;
            document.getElementById("countdown").innerHTML = secondsToTimeString(parseInt(res.exptime));
            questiondata = res;
        }
    });
}
function submitAnswer() {
    let data = {
        "qgroup": terminaldata.questiongroup,
        "question": terminaldata.question,
        "points": questiondata.points,
        "answercorrect": answerright,
        "answer": givenanswer,
        "timeleft": timeremaining
    };
    fetch("submitquestion.php", {
        "method": "POST",
        "body": JSON.stringify(data)
    })
        .then(res => {
        if (res.ok) {
            return res.text();
        }
    })
        .then(res => {
        resetTerminal();
    });
}
function resetTerminal() {
    stopCountDown();
    inuse = false;
    locked = true;
    createWaitingScreen();
    questiondata = undefined;
}
function createWaitingScreen() {
    document.getElementById("contentHolder").innerHTML = "<p class='idletext'>" + terminaldata.text + "</p>";
}
function setupAnswerEnvironment() {
    if (!questiondata) {
        setTimeout(setupAnswerEnvironment, 20);
        return;
    }
    var givecode = document.getElementById("qcode").value;
    if (givecode == terminaldata.question) {
        let holder = emptyContentHolder(document.getElementById("contentHolder"));
        var iQuestion = "<p class='unlocked'>" + questiondata.question + "</p>";
        holder.innerHTML += iQuestion;
        if (questiondata.image !== "") {
            var iImage = "<img src='" + buildImageURI(questiondata.image) + "' class='unlocked_img' />";
            holder.innerHTML += iImage;
        }
        holder.innerHTML += "<div class='input_holder' id='inputHolder'></div>";
        if (questiondata.qtype == "text") {
            var iInput = "<div class='input_object'><input type='text' class='text-input' id='qanswer' focus placeholder='Antwoord'></div>";
            holder.innerHTML += iInput;
        }
        else if (questiondata.qtype == "radio") {
            var arrayLength = questiondata.answers.length;
            for (var i = 0; i < arrayLength; i++) {
                let answer = questiondata.answers[i];
                var iInput = "<div class='input_object'><input type='radio' class='radio-input' name='qanswer' id='" + answer + "' value='" + answer + "'><label for='" + answer + "'>" + answer + "</label></div>";
                holder.innerHTML += iInput;
            }
        }
        var iButton = "<input type='button' class='button' onclick=\"checkAnswer();\"value='Go'>";
        holder.innerHTML += iButton;
    }
    else {
        document.getElementById("qcode").value = "";
        document.getElementById("qcode").placeholder = "Try again";
    }
}
function checkAnswer() {
    if (questiondata.qtype == "text") {
        var qAnswer = document.getElementById("qanswer").value;
    }
    else if (questiondata.qtype == "radio") {
        var qAnswer = document.querySelector('.radio-input:checked').value;
    }
    givenanswer = qAnswer;
    answerright = false;
    if (questiondata.right_answers.indexOf(qAnswer) > -1) {
        answerright = true;
        document.getElementById("feedbackholder-right").style.display = "block";
        submitAnswer();
        stopCountDown();
    }
    else {
        document.getElementById("feedbackholder-wrong").style.display = "block";
        var closefeedback = setTimeout(function () { closeFeedback('wrong'); }, 5000);
    }
}
function closeFeedback(type) {
    var element = "feedbackholder-" + type;
    document.getElementById(element).style.display = "none";
}
function buildImageURI(uri) {
    return "../images/" + uri;
}
function emptyContentHolder(holder) {
    while (holder.firstElementChild != holder.lastElementChild) {
        holder.removeChild(holder.lastElementChild);
    }
    return holder;
}
function lockTerminal() {
    document.getElementById("contentHolder").innerHTML = "<div id='countdown' class='countdown'>--:--</div>";
    document.getElementById("contentHolder").innerHTML += "<p id='lockscreen'><span class='restext'>Gereserveerd voor groep " + terminaldata.group + "</span><br /><span class='unlocktip'>Tik op het scherm om te ontgrendelen</span></p>";
}
function toggleLockState() {
    if (inuse) {
        if (locked) {
            locked = false;
            document.getElementById("countdown").className = "miniclock";
            document.getElementById("contentHolder").removeChild(document.getElementById("lockscreen"));
            document.getElementById("contentHolder").innerHTML += "<p class='unlocked'>Voer de opdrachtcode in:</p><input type='text' class='text-input' readonly value='" + terminaldata.questiongroup + "'><br /><input type='text' class='text-input' id='qcode' focus placeholder='Opdrachtcode'><br /><input type='button' class='button' onclick=\"setupAnswerEnvironment();\"value='Go'>";
            document.getElementById("qcode").focus();
        }
    }
}
function stopCountDown() {
    countdownrunning = false;
}
function countDown() {
    if (timeremaining > 0 && countdownrunning) {
        timeremaining -= 1000;
        document.getElementById("countdown").innerHTML = secondsToTimeString(Math.round(timeremaining / 1000));
    }
}
function secondsToTimeString(seconds) {
    if (seconds <= 0) {
        return "00:00";
    }
    let minutes = 0;
    while (seconds > 59) {
        seconds -= 60;
        minutes++;
    }
    let string = "";
    if (minutes > 9) {
        string += minutes;
    }
    else {
        string += "0" + minutes;
    }
    string += ":";
    if (seconds > 9) {
        string += seconds;
    }
    else {
        string += "0" + seconds;
    }
    return string;
}
