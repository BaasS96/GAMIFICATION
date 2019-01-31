<?php
    session_start();
    if (isset($_SESSION["sessionactive"]) && $_SESSION["sessionactive"] == "1") {
        //continue to terminal
        header("Location: terminal.html?game=" . $_SESSION["gamepin"] . "&id=" . $_SESSION['terminalid']);
        exit();
    }
    else if (isset($_POST["terminalid"]) && isset($_POST["gamepin"])) {
        //set session variables
        $_SESSION["gamepin"] = $_POST["gamepin"];
        $_SESSION["terminalid"] = $_POST["terminalid"];
        $_SESSION["sessionactive"] = "1";
        //get terminaldata
        $gamepath = '../games/' . $_POST['gamepin'] . '/game.json';
        $terminaljson = file_get_contents('../games/' . $_POST['gamepin'] . '/game.json');
        $terminaldata = json_decode($terminaljson,true);
        //register the terminal as activated
        $terminals = $terminaldata['terminals'];

        if (!is_array($terminals)) {
            $terminals = (array)$terminals;
        }

        for ($i = 0; $i < count($terminals); $i++) {
            $currentterminal = $terminals[$i];
            if ($currentterminal['id'] == $_POST['terminalid']) {
                $terminals[$i]['activated']= true;
                break;
            }
        }

        $terminaldata['terminals'] = $terminals;
        
        //write the modified data to the terminal file
        $data_json = json_encode($terminaldata);
        file_put_contents($gamepath, $data_json);
        //continue to terminal
        header("Location: terminal.html?game=" . $_SESSION["gamepin"] . "&id=" . $_SESSION['terminalid']);
        exit();
    }
    else {
        //return to logon screen
        session_destroy();
        header("Location: terminallogon.html");
        exit();
    }
?>