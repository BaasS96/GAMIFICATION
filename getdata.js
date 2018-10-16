function requestTerminal(gameID, maxTerminals, groupID, questionGroupID, questionID, terminalID) {
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
                                var hr = new XMLHttpRequest();
                                // Create some variables we need to send to our PHP file
                                //!!!!!Which vars have to be sent to the php file????
                                var vars = "gamepin=" + mGamePin + "&groupid=" + terminalData.groupid + "&certificate=" + terminalData.certificatenumber + "&question=" + terminalData.questionnumber + "&answer=" + mAnswer + "&timeleft=" + mTimeLeftU + "&points=" + terminalData.points;
                                console.log(vars);
                                hr.open("POST", "reserveterminal.php", true);
                                hr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                                // Access the onreadystatechange event for the XMLHttpRequest object
                                hr.onreadystatechange = function() {
                                        if (hr.readyState == 4 && hr.status == 200) {
                                            var return_data = hr.responseText;
                                            console.log(return_data);
                                            resetTerminal();
                                        }
                                    }
                                    // Send the data to PHP now... and wait for response to update the status div
                                hr.send(vars); // Actually execute the request
                            } else if (terminalData.activated == true && terminalData.inuse == true) {
                                //the terminal is allready in use, return an error message with the maximum time until the terminal unlocks
                            } else {
                                //something went wrong, perhaps the terminal was not activated?
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