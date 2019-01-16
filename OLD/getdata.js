var updater;
var terminalids = [];
var terminalcodes = [];

function requestTerminal(gameID, maxTerminals, groupID, questionGroupID, questionID, terminalID, qcode, timetillexp) {
    console.log(gameID);
    console.log(maxTerminals);
    console.log(groupID);
    console.log(questionGroupID);
    console.log(questionID);
    console.log(terminalID);
    //get the groupdata
    var file = "games/" + gameID + "/group/" + groupID + ".json?" + (new Date()).getTime();
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            var groupData = JSON.parse(this.responseText);
            console.log(groupData);
            //check if the maximum number of terminals a group can use at once is not exeeded yet
            if (groupData.terminals.length <= maxTerminals) {
                console.log("yes");
                //the group may still connect to a terminal
                //check if the group is allready connected to the terminal linked to this question
                var tempTerminals = groupData.terminals;
                console.log(tempTerminals);
                if (tempTerminals.indexOf(terminalID) != -1) {
                    //the group is allready connected to the terminal. Return an error message
                    console.log("allready connected");
                } else {
                    //the group is not connected to the terminal
                    console.log("not connected");
                    //check if the terminal is available
                    var file = "games/" + gameID + "/terminal/" + terminalID + ".json?" + (new Date()).getTime();
                    var xmlhttp = new XMLHttpRequest();
                    xmlhttp.onreadystatechange = function() {
                        if (this.readyState == 4 && this.status == 200) {
                            var terminalData = JSON.parse(this.responseText);
                            console.log(terminalData);
                            if (terminalData.activated == true && terminalData.inuse == false) {
                                //if available reserve the terminal for the group
                                console.log("JAAAA");
                                //create a code for the terminal
                                //can be static or dynamic: if dynamic, generate here, if static, get from question file.
                                console.log(qcode);
                                if (qcode == "") {
                                    //dynamic
                                    var qcodenew = makeHexCode();
                                } else {
                                    var qcodenew = qcode;
                                }
                                console.log("qcode = " + qcodenew);
                                //create the timestamp for the exptime
                                var currenttimestamp = new Date().getTime();
                                console.log(currenttimestamp);
                                var exptime = currenttimestamp + parseInt(timetillexp * 1000);
                                console.log(exptime);
                                //post the request, with the parameters as 'vars'
                                var hr = new XMLHttpRequest();
                                var vars = "gamepin=" + gameID + "&terminalID=" + terminalID + "&groupcode=" + groupID + "&qcode=" + qcodenew + "&exptime=" + exptime + "&qgroup=" + questionGroupID + "&qnum=" + questionID;
                                console.log(vars);
                                hr.open("POST", "reserveterminal.php", true);
                                hr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                                // Access the onreadystatechange event for the XMLHttpRequest object
                                hr.onreadystatechange = function() {
                                        if (hr.readyState == 4 && hr.status == 200) {
                                            var return_data = hr.responseText;
                                            console.log(return_data);
                                            console.log(qcode);
                                            //feedback to the user
                                            var element = "feedbackholder_" + questionGroupID + "-" + questionID;
                                            document.getElementById(element).innerHTML = "<i class='material-icons'>done</i><em>De terminal is gereserveerd. Gebruik deze code om in te loggen: <strong>" + qcodenew + "</strong></em>";
                                            openConnection(gameID, groupID, questionGroupID, terminalID, qcodenew);
                                            //if (updater) updater.close();
                                            //openConnection(gameID, groupID, questionGroupID, terminalID, qcodenew);
                                        }
                                    }
                                    // Send the data to PHP now... and wait for response to update the status div
                                hr.send(vars); // Actually execute the request
                            } else if (terminalData.activated == true && terminalData.inuse == true) {
                                //the terminal is allready in use, return an error message with the maximum time until the terminal unlocks
                                console.log("terminal not available: allready in use");
                            } else {
                                //something went wrong, perhaps the terminal was not activated?
                                console.log("terminal not avaiable: not activated");
                            }
                        }
                    };
                    xmlhttp.open("GET", file, true)
                    xmlhttp.send();
                }
            } else {
                console.log("noooo");
            }
        }
    };
    xmlhttp.open("GET", file, true)
    xmlhttp.send();
    //check if the group allready is connected to the terminal
}

//TERMINAL OVERVIEW

var terminals = [];

function openConnection(gameid, groupid, questionGroupID, terminalID, terminalEntryCode) {
    terminalids.push(terminalID);
    terminalcodes[terminalID] = terminalEntryCode;
    let terminals = terminalids.join(",");
    let url = "watcher/group/terminalwatcher.php?game=" + gameid + "&group=" + groupid + "&qgroup=" + questionGroupID + "&terminals=" + terminals;
    updater = new EventSource(url);
    updater.addEventListener('message', (e) => {
        var newStats = JSON.parse(e.data);
        updateOverviewHeader(newStats.numofterminals);
        updateContents(newStats.terminaldata);
    });
}

function updateOverviewHeader(quantity) {
    let box = document.getElementById('terminal_monitor_header');
    if (quantity === 1) {
        box.innerText = "1 Actieve Terminal";
    } else {
        box.innerText = quantity + " Actieve Terminals";
    }
}

function updateContents(terminaldata) {
    for (var data of terminaldata) {
        var element = document.getElementById("element_q" + data.qcode);
        var tinseconds = data.timeleft;
        if (element) {
            if (desynchronized(element.childNodes[1].innerText, tinseconds)) {
                terminals[data.qcode].timeleft = tinseconds;
                clearInterval(terminals[data.qcode].timerID);
                terminals[data.qcode].timerID = setInterval(terminalTimer.bind(data.qcode), 1000)
                element.childNodes[1].innerText = secondsToProperNotation(seconds);
            }
        } else {
            //Create new entry
            let entry = {
                "timerID": 0,
                "entry": null,
                "timeleft": data.timeleft
            };
            terminals[data.qcode] = entry;
            let newentry = newTerminalEntry(data.qcode, tinseconds, terminalcodes[data.terminalid]);
            entry.entry = newentry;
            terminals[data.qcode] = entry;
            document.getElementById("terminal_monitor").appendChild(newentry);
        }
    }
}

function terminalTimer() {
    if (terminals[this].timeleft > 0) {
    var element = document.getElementById("element_q" + this);
    element.childNodes[1].innerText = secondsToProperNotation(--terminals[this].timeleft);
    } else {
        console.log("Terminal for qcode " + this + " expired");
    }
}

function newTerminalEntry(questionnum, timeleft, entrycode) {
    let element = document.createElement("div");
    element.className = "terminal_monitor_element";
    element.id = "element_q" + questionnum;
    let component = document.createElement("div");
    component.className = "terminal_monitor_element_component";
    component.innerHTML = "<span>Vraag #" + questionnum + "</span><span>Code: " + entrycode + "</span>";
    element.appendChild(component);
    component.innerHTML = "<span>" + secondsToProperNotation(timeleft / 1000) + "</span>";
    element.appendChild(component);
    //Start timer
    //terminals[questionnum].timerID = setInterval(terminalTimer.bind(questionnum), 1000);
    return element;
}

function desynchronized(local, remote) {
    let split = local.split(":");
    seconds = 60 * split[0] + split[1];
    return seconds > remote - 2 && seconds < remote + 2;
}

function secondsToProperNotation(seconds) {
    let minutes = 0;
    while (seconds > 59) {
        minutes++;
        seconds -= 60;
    }
    let returnstring = "";
    if (minutes < 10) {
        returnstring += "0" + minutes;
    } else {
        returnstring += minutes;
    }
    if (seconds < 10) {
        returnstring += "0" + seconds;
    } else {
        returnstring += seconds;
    }
    return returnstring;
}