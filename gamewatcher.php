<?php
if (!isset($_GET["gamepin"])) {
    header("Location: newgame.html");
    exit();
}
else {
    $gamepin = $_GET["gamepin"];
    $groupnum = 0;
    //back link
    echo "<a href='newgame.html#watchgame'>Back</a><br />---<br />";
    //get all existing groups within this game
    $groups = array_filter(glob("games/" . $gamepin . "/group" . "/*"), 'is_file');
    foreach ($groups as $currentgroup) {
        $json = file_get_contents($currentgroup);
        $data = json_decode($json,true);
        if (empty($data["lastactive"]) || $data["lastactive"] == "") {
            $lastactive = "0";
            $formattedtime = "never";
        }
        else {
            $lastactive = $data["lastactive"];
            $formattedtime = gmdate("Y-m-d H:i:s", $data["lastactive"]);
        }
        echo "
            <p>
            **************************************************<br />
                ---- Groupcode: <i>" . $data["groupcode"] . "</i><br />
                &emsp;|---- Restaurantname: <i>" . $data["restaurantname"] . "</i><br />
                &emsp;|---- Membernames: <br />
        ";
        //build membernames
            foreach ($data["membernames"] as $currentname) {
                echo "
                    &emsp;&nbsp;&emsp;|--- <i>" . $currentname . "</i><br />
                ";
            }
        echo "
            &emsp;|---- Certificates: <br />
        ";
        //build certificates
            foreach ($data["certificates"] as $currentcertificate) {
                echo "
                    &emsp;&nbsp;&emsp;|--- <i>" . $currentcertificate . "</i><br />
                ";
            }
        echo "
            &emsp;|---- Data: <br />
        ";
        //build data
        foreach ($data["data"] as $currentdata) {
            echo "
                &emsp;&nbsp;&emsp;|--- <i>" . $currentdata . "</i><br />
            ";
        }
        echo "
            &emsp;|---- Lastactive: <i>" . $lastactive . " (" . $formattedtime . ") [UTC]</i><br />
            **************************************************
            </p>
        ";
        $groupnum ++;
    }
    echo "==================================================<br />";
    echo "Groups: $groupnum";
    echo "<br />---<br /><a href='newgame.html'>Back</a>";
}
?>