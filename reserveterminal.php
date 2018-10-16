<?php
    //only execute if these vars are set (post vars)
    if (isset($_POST["gamepin"]) && isset($_POST["terminalID"]) && isset($_POST["groupcode"]) && isset($_POST["qcode"]) && isset($_POST["exptime"]) && isset($_POST["qgroup"]) && isset($_POST["qnum"])) {
        //get the questiondata
        $questiondatafile = "games/" . $_POST["gamepin"] . "/questions" . "/" . $_POST["qgroup"] . "/" . $_POST["qnum"] . ".json";
        $json = file_get_contents($questiondatafile);
        $questiondata = json_decode($json,true);
        //print_r( $questiondata);
        //write to the terminalfile
        //get the terminaldatafile
        $terminalfile = "games/" . $_POST["gamepin"] . "/terminal" . "/" . $_POST["terminalID"] . ".json";
        $terminaljson = file_get_contents($terminalfile);
        $terminaldata_new = json_decode($terminaljson,true);
        //modify the data
        $terminaldata_new["groupid"] = $_POST["groupcode"];
        $terminaldata_new["qcode"] = $_POST["qcode"];
        $terminaldata_new["exptime"] = $_POST["exptime"];
        $terminaldata_new["qtype"] = $questiondata["qtype"];
        $terminaldata_new["question"] = $questiondata["question"];
        $terminaldata_new["answers"] = $questiondata["answers"];
        $terminaldata_new["ranswers"] = $questiondata["ranswers"];
        $terminaldata_new["points"] = $questiondata["points"];
        $terminaldata_new["image"] = $questiondata["image"];
        //print_r( $terminaldata_new);
        //write the modified data to the terminalfile
        $myfile = fopen($terminalfile, "r+");
        $data_json = json_encode($terminaldata_new);
        fwrite($myfile, $data_json);
        fclose($myfile);
        //write the terminal to the groupfile
        //get the terminaldatafile
        $groupfile = "games/" . $_POST["gamepin"] . "/group" . "/" . $_POST["groupcode"] . ".json";
        $groupjson = file_get_contents($groupfile);
        $groupdata_new = json_decode($groupjson,true);
        //modify the data
        array_push($groupdata_new["terminals"],$_POST["terminalID"]);
        print_r( $groupdata_new);
        //write the modified data to the terminalfile
        $myfile = fopen($groupfile, "r+");
        $data_json = json_encode($groupdata_new);
        fwrite($myfile, $data_json);
        fclose($myfile);
        //return a succes message
        echo "Terminal " . $_POST["terminalID"] . " reserved.";
    }
    else {
        //stop
        exit();
    }
?>