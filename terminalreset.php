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
    $groupid = $terminaldata->groupid;

    //modification of the group data
    $groupfile = "games/" . $gamepin . "/group//" . $groupid . ".json";
    $groupdata = file_get_contents($groupfile);
    $terminals = $groupdata->terminals;
    array_splice($terminals, $terminalid);
    $groupdata->terminals = $terminals;
    file_put_contents($groupfile, json_encode($groupdata));

    $active = $terminaldata["activated"];
    $certnum = $terminaldata["certificatenumber"];
    $questnum = $terminaldata["questionnumber"];
    $idletext = $terminaldata["idletext"];
    //set inuse to false, all other vars back to empty or their initial value
    $new_terminaldata = array("terminalcode" => $terminalid, "certificatenumber" => $certnum, "questionnumber" => $questnum, "idletext" => $idletext, "activated" => $active, "inuse" => false, "groupid" => "", "qcode" => "", "exptime" => "", "qtype" => "", "question" => "", "answers" => array(""), "ranswers" => array(""), "points" => "");
    //write the modified data to the groupfile
    $myfile = fopen($terminalfile, "w");
    $data_json = json_encode($new_terminaldata);
    fwrite($myfile, $data_json);
    fclose($myfile);
    //return Done
    echo "terminalreset.php: Done.";
?>