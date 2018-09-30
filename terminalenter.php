<?php
session_start();
    if (isset($_SESSION["sessionactive"]) && $_SESSION["sessionactive"] == "1") {
        //continue to terminal
        header("Location: terminal.php");
        exit();
    }
    else if (isset($_POST["terminalid"]) && isset($_POST["gamepin"])) {
        //set session variables
        $_SESSION["gamepin"] = $_POST["gamepin"];
        $_SESSION["terminalid"] = $_POST["terminalid"];
        $_SESSION["sessionactive"] = "1";
        $_SESSION["terminaldir"] = "games/" . $_POST["gamepin"] . "/terminal" . "/" . $_POST["terminalid"] . ".json";
        //get terminaldata
        $terminaljson = file_get_contents($_SESSION["terminaldir"]);
        $terminaldata = json_decode($terminaljson,true);
        //register the terminal as activated
        $terminaldata["activated"] = "1";
        //write the modified data to the terminal file
        $myfile = fopen($_SESSION["terminaldir"], "r+");
        $data_json = json_encode($terminaldata);
        fwrite($myfile, $data_json);
        fclose($myfile);
        //continue to terminal
        header("Location: terminal.php");
        exit();
    }
    else {
        //return to logon screen
        session_destroy();
        header("Location: terminallogon.html");
        exit();
    }
?>