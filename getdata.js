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
                                            document.getElementById(element).innerHTML = "<i class='material-icons'>done</i><em>De terminal is gereserveerd. Gebruik deze code om in te loggen: <strong>" + qcode + "</strong></em>";
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