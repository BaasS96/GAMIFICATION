<?php
    //get vars from POST
    $gamepin = $_POST["gamepin"];
    $terminalid = $_POST["terminalid"];
    //create vars
    $terminalfile = "games/" . $gamepin . "/terminal" . "/" . $terminalid . ".json";
    //get the groupfile
    $terminaljson = file_get_contents($terminalfile);
    $terminaldata = json_decode($terminaljson,true);
    //modify the data
    $groupid = $terminaldata["groupid"];

    //modification of the group data
    $groupfile = "games/" . $gamepin . "/group" . "/" . $groupid . ".json";
    $groupdata = file_get_contents($groupfile);
    $groupdat = json_decode($groupdata,true);
    $terminals = $groupdat["terminals"];
    $index = array_search($terminalid, $terminals);
    array_splice($terminals, $index);
    $groupdat["terminals"] = $terminals;
    file_put_contents($groupfile, json_encode($groupdat));

    $active = $terminaldata["activated"];
    $certnum = $terminaldata["certificatenumber"];
    $questnum = $terminaldata["questionnumber"];
    $idletext = $terminaldata["idletext"];
    
    $emptyterminal = file_get_contents("data/emptyterminal.json");
    $terminaljson = json_decode($emptyterminal);
    $terminaljson->terminalcode = $terminalid;
    $terminaljson->certificatenumber = $certnum;
    $terminaljson->questionnumber = $questnum;
    $terminaljson->idletext = $idletext;
    $terminaljson->activated = $active;
    $terminaljson->inuse = false;

    //write the modified data to the groupfile
    $myfile = fopen($terminalfile, "w");
    $data_json = json_encode($terminaljson);
    fwrite($myfile, $data_json);
    fclose($myfile);
    //return Done
    echo "terminalreset.php: Done.";
?>