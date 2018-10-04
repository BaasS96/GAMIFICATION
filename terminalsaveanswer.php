<?php
    //get vars from POST
    $gamepin = $_POST["gamepin"];
    $groupid = $_POST["groupid"];
    $certificate = $_POST["certificate"];
    $question = $_POST["question"];
    $answer = $_POST["answer"];
    $timeleft = $_POST["timeleft"];
    $points = $_POST["points"];
    //create vars
    $groupfile = "games/" . $gamepin . "/group" . "/" . $groupid . ".json";
    //get the groupfile
    $groupjson = file_get_contents($groupfile);
    $groupdata = json_decode($groupjson,true);
    //modify the data
    $groupdata["certificates"][$certificate][$question]["answer"] = $answer;
    $groupdata["certificates"][$certificate][$question]["points"] = $points;
    $groupdata["certificates"][$certificate][$question]["timeleft"] = $timeleft;
    $groupdata["lastactive"] = time();
    //write the modified data to the groupfile
    $myfile = fopen($groupfile, "r+");
    $data_json = json_encode($groupdata);
    fwrite($myfile, $data_json);
    fclose($myfile);
    //return Done
    echo "terminalsaveanswer.php: Done.";
?>