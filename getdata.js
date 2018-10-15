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
                //!!!!!something... to do this?
                if (groupData.terminal.indexOf)
                //check if the terminal is available
                    var file = "games/" + gameID + "/terminal/" + terminalID + ".json?" + (new Date()).getTime();
                var xmlhttp = new XMLHttpRequest();
                xmlhttp.onreadystatechange = function() {
                    if (this.readyState == 4 && this.status == 200) {
                        var terminalData = JSON.parse(this.responseText);
                        console.log(terminalData);
                    }
                };
                xmlhttp.open("GET", file, true)
                xmlhttp.send();
            } else {
                console.log("noooo");
            }
        }
    };
    xmlhttp.open("GET", file, true)
    xmlhttp.send();
    //check if the group allready is connected to the terminal
}