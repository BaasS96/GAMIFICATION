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
    $active = $terminaldata["activated"];
    $certnum = $terminaldata["certificatenumber"];
    $questnum = $terminaldata["questionnumber"];
    $idletext = $terminaldata["idletext"];
    $new_terminaldata = array("terminalcode" => $terminalid, "certificatenumber" => $certnum, "questionnumber" => $questnum, "idletext" => $idletext, "activated" => $active, "inuse" => "0", "groupid" => "", "qcode" => "", "exptime" => "", "qtype" => "", "question" => "", "answers" => array(""), "ranswers" => array(""), "points" => "");
    //write the modified data to the groupfile
    $myfile = fopen($terminalfile, "w");
    $data_json = json_encode($new_terminaldata);
    fwrite($myfile, $data_json);
    fclose($myfile);
    //return Done
    echo "terminalreset.php: Done.";
?>