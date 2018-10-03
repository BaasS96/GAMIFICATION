//Declaration of GLOBAL variables
//Interval variables
var autoUpdate = null;
var countdown = null;
var closeTimeout = null;
//Main internal GLOBAL variables (m...)
var mCountDownActive = "0";
var mTerminalDir = "";
var mTimeLeft = "00:00";
//Main GET-variables (from AJAX-request (terminal...))
var terminalData = [];
var terminalInUse = "0";
var terminalUnlocked = "0";

//function to setup the connection (passing variables to this script)
function setupData(terminalDir) {
    mTerminalDir = terminalDir;
}

//main function to fetch new terminal data each 5 seconds (5000ms)
function refreshpage() {
    getTerminalData(mTerminalDir);
    console.log("active");
    autoUpdate = setInterval(function() { getTerminalData(mTerminalDir); }, 5000);
}

//function to run the countdown clock
function countDown() {
    var timeNow = Date.now();
    var timeLeft = terminalData.exptime - timeNow;
    //console.log(timeLeft);
    if (timeLeft >= 0) {
        var timeLeftP = new Date(timeLeft);
        var minutes = "0" + timeLeftP.getMinutes();
        var seconds = "0" + timeLeftP.getSeconds();
        mTimeLeft = minutes.substr(-2) + ":" + seconds.substr(-2);
        document.getElementById("countdown").innerHTML = mTimeLeft;
    }
}

//function for the AJAX-request
function getTerminalData() {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            terminalData = JSON.parse(this.responseText);
            console.log("XHR Request");
            var currentTime = Date.now();
            //console.log(currentTime);
            //console.log(terminalData.exptime);
            //console.log(terminalData.exptime - currentTime);
            if (currentTime <= terminalData.exptime) {
                //console.log("2");
                if (mCountDownActive == "0") {
                    //console.log("Activated countdown");
                    countdown = setInterval(function() { countDown(); }, 1000);
                    mCountDownActive = "1";
                }
                terminalInUse = "1";
                document.getElementById("contentHolder").innerHTML = "<div id='countdown' class='countdown'>" + mTimeLeft + "</div>";
                document.getElementById("contentHolder").innerHTML += "<p><span class='restext'>Gereserveerd voor groep " + terminalData.groupid + "</span><br /><span class='unlocktip'>Tik op het scherm om te ontgrendelen</span></p>";
            } else {
                //console.log("3");
                clearInterval(countdown);
                mCountDownActive = "0";
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
    //console.log("autoclose init");
    clearInterval(autoUpdate);
    clearTimeout(closeTimeout);
    var closeTimeout = setTimeout(function() {
        //console.log("autoClose done")
        refreshpage();
        terminalUnlocked = "0";
        clearTimeout(closeTimeout);
    }, 60000);
}

//function for the unlock feature, if the terminal is locked
function unlockTerminal() {
    if (terminalInUse == "1") {
        if (terminalUnlocked == "0") {
            terminalUnlocked = "1";
            //console.log("InUse");
            autoClose();
            document.getElementById("contentHolder").innerHTML = "<div class='miniclock' id='countdown'>" + mTimeLeft + "</div><p class='unlocked'>Voer de opdrachtcode in:</p><input type='text' class='text-input' readonly value='" + terminalData.groupid + "'><br /><input type='text' class='text-input' id='qcode' focus placeholder='Opdrachtcode'><br /><input type='button' class='button' value='Go'>";
            document.getElementById("qcode").focus();
        } else {
            //console.log("allready unlocked")
        }
    } else {
        //console.log("NotInUse");
    }
}