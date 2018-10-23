//Declaration of GLOBAL variables
//Interval variables
var autoUpdate = null;
var countdown = null;
var closeTimeout = null;
//Main internal GLOBAL variables (m...)
var mCountDownActive = "0";
var mTerminalDir = "";
var mGamePin = "";
var mTimeLeft = "00:00";
var mTimeLeftU = null;
var mAnswer = "";
//Main GET-variables (from AJAX-request (terminal...))
var terminalData = [];
var terminalInUse = "0";
var terminalUnlocked = "0";

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

//function to setup the connection (passing variables to this script)
function setupData(terminalDir, gamepin) {
    mTerminalDir = terminalDir;
    mGamePin = gamepin;
}

//main function to fetch new terminal data each 5 seconds (5000ms)
function refreshpage() {
    getTerminalData();
    console.log("Active");
    clearInterval(autoUpdate);
    autoUpdate = setInterval(function() { getTerminalData(); }, 5000);
}

//function to run the countdown clock
function countDown() {
    var timeNow = Date.now();
    mTimeLeftU = terminalData.exptime - timeNow;
    if (mTimeLeftU >= 0) {
        var timeLeftP = new Date(mTimeLeftU);
        var minutes = "0" + timeLeftP.getMinutes();
        var seconds = "0" + timeLeftP.getSeconds();
        mTimeLeft = minutes.substr(-2) + ":" + seconds.substr(-2);
        document.getElementById("countdown").innerHTML = mTimeLeft;
    }
}

//reset terminal (flag as inuse=0)
function resetTerminal() {
    // Create our XMLHttpRequest object
    var hr = new XMLHttpRequest();
    // Create some variables we need to send to our PHP file
    var vars = "gamepin=" + mGamePin + "&terminalid=" + terminalData.terminalcode;
    hr.open("POST", "terminalreset.php", true);
    hr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // Access the onreadystatechange event for the XMLHttpRequest object
    hr.onreadystatechange = function() {
            if (hr.readyState == 4 && hr.status == 200) {
                var return_data = hr.responseText;
                console.log(return_data);
                terminalUnlocked = "0";
                clearTimeout(closeTimeout);
                refreshpage();
            }
        }
        // Send the data to PHP now... and wait for response to update the status div
    hr.send(vars); // Actually execute the request
}

//function for the AJAX-request
function getTerminalData() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            terminalData = JSON.parse(this.responseText);
            var currentTime = Date.now();
            if (currentTime <= terminalData.exptime) {
                if (mCountDownActive == "0") {
                    countdown = setInterval(function() { countDown(); }, 1000);
                    mCountDownActive = "1";
                }
                terminalInUse = "1";
                document.getElementById("contentHolder").innerHTML = "<div id='countdown' class='countdown'>" + mTimeLeft + "</div>";
                document.getElementById("contentHolder").innerHTML += "<p><span class='restext'>Gereserveerd voor groep " + terminalData.groupid + "</span><br /><span class='unlocktip'>Tik op het scherm om te ontgrendelen</span></p>";
            } else {
                clearInterval(countdown);
                if (mCountDownActive == "1") {
                    resetTerminal();
                    mCountDownActive = "0";
                }
                document.getElementById("contentHolder").innerHTML = "<p class='idletext'>" + terminalData.idletext + "</p>";
                terminalInUse = "0";
            }
        }
    };
    xmlhttp.open("GET", mTerminalDir + "?" + (new Date()).getTime(), true);
    xmlhttp.send();
}

//function to automatically close the input after 60 seconds (6000ms)
function autoClose() {
    clearInterval(autoUpdate);
    clearTimeout(closeTimeout);
    var closeTimeout = setTimeout(function() {
        refreshpage();
        terminalUnlocked = "0";
        clearTimeout(closeTimeout);
    }, 60000);
}

//function to close the feedback window
function closeFeedback(type) {
    var element = "feedbackholder-" + type;
    document.getElementById(element).style.display = "none";
}

//function to submit the answer if it was correct
function submitAnswer() {
    // Create our XMLHttpRequest object
    var hr = new XMLHttpRequest();
    // Create some variables we need to send to our PHP file
    var vars = "gamepin=" + mGamePin + "&groupid=" + terminalData.groupid + "&certificate=" + terminalData.certificatenumber + "&question=" + terminalData.questionnumber + "&answer=" + mAnswer + "&timeleft=" + mTimeLeftU + "&points=" + terminalData.points;
    console.log(vars);
    hr.open("POST", "saveanswer.php", true);
    hr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    // Access the onreadystatechange event for the XMLHttpRequest object
    hr.onreadystatechange = function() {
            if (hr.readyState == 4 && hr.status == 200) {
                var return_data = hr.responseText;
                console.log(return_data);
                document.getElementById("feedbackholder-right").style.display = "block";
                var closefeedback = setTimeout(function() { closeFeedback('right'); }, 5500)
                var i = 4;
                var countDownFeedback = setInterval(function() {
                    document.getElementById("autoclosetime").innerHTML = i;
                    i--;
                }, 1000)
                var delayReset = setTimeout(function() {
                    clearInterval(countdown);
                    mCountDownActive = "0";
                    resetTerminal();
                }, 5000);
            }
        }
        // Send the data to PHP now... and wait for response to update the status div
    hr.send(vars); // Actually execute the request
}

//function to check the answer (and continue)
function checkQanswer() {
    if (terminalData.qtype == "text") {
        var qAnswer = escapeHtml(document.getElementById("qanswer").value);
    } else if (terminalData.qtype == "radio") {
        var qAnswer = document.querySelector('.radio-input:checked').value;
    }
    if (terminalData.ranswers.indexOf(qAnswer.toUpperCase()) >= 0) {
        mAnswer = qAnswer;
        submitAnswer();
    } else {
        document.getElementById("feedbackholder-wrong").style.display = "block";
        var closefeedback = setTimeout(function() { closeFeedback('wrong'); }, 5000)
    }
}

//function to check the question code (and continue)
function checkQcode() {
    var qCode = escapeHtml(document.getElementById("qcode").value);
    if (qCode.toUpperCase() == terminalData.qcode.toUpperCase()) {
        document.getElementById("contentHolder").innerHTML = "";
        var iClock = "<div class='miniclock' id='countdown'>" + mTimeLeft + "</div>";
        document.getElementById("contentHolder").innerHTML += iClock;
        var iQuestion = "<p class='unlocked'>" + terminalData.question + "</p>";
        document.getElementById("contentHolder").innerHTML += iQuestion;
        if (terminalData.qimage !== "") {
            var iImage = "<img src='' class='unlocked_img' />";
            document.getElementById("contentHolder").innerHTML += iImage;
        }
        document.getElementById("contentHolder").innerHTML += "<div class='input_holder' id='inputHolder'></div>";
        if (terminalData.qtype == "text") {
            var iInput = "<div class='input_object'><input type='text' class='text-input' id='qanswer' focus placeholder='Antwoord'></div>";
            document.getElementById("inputHolder").innerHTML += iInput;
        } else if (terminalData.qtype == "radio") {
            var arrayLength = terminalData.answers.length;
            for (var i = 0; i < arrayLength; i++) {
                var iInput = "<div class='input_object'><input type='radio' class='radio-input' name='qanswer' id='" + terminalData.answers[i] + "' value='" + terminalData.answers[i] + "'><label for='" + terminalData.answers[i] + "'>" + terminalData.answers[i] + "</label></div>";
                document.getElementById("inputHolder").innerHTML += iInput;
            }
        }
        var iButton = "<input type='button' class='button' onclick=\"checkQanswer();\"value='Go'>";
        document.getElementById("contentHolder").innerHTML += iButton;
    } else {
        document.getElementById("qcode").value = "";
        document.getElementById("qcode").placeholder = "Try again";
    }
}

//function for the unlock feature, if the terminal is locked
function unlockTerminal() {
    if (terminalInUse == "1") {
        if (terminalUnlocked == "0") {
            terminalUnlocked = "1";
            autoClose();
            document.getElementById("contentHolder").innerHTML = "<div class='miniclock' id='countdown'>" + mTimeLeft + "</div><p class='unlocked'>Voer de opdrachtcode in:</p><input type='text' class='text-input' readonly value='" + terminalData.groupid + "'><br /><input type='text' class='text-input' id='qcode' focus placeholder='Opdrachtcode'><br /><input type='button' class='button' onclick=\"checkQcode();\"value='Go'>";
            document.getElementById("qcode").focus();
        }
    }
}