<?php
session_start();
//check if the login was allready completed, if so, get all the data and continue. If not, return to the gamepin-page.
if (isset($_SESSION["loginready"])) {
    if ($_SESSION["loginready"] == "1") {
        //data does not have to be written to file, so continue
        $gamepin = $_SESSION["gamepin"];
        $groupcode = $_SESSION["gamegroup"];
        unset($_SESSION["gamegroup"]);
        $gamedir = "games/" . $gamepin;
        $groupdir = $gamedir . "/" . $groupcode . ".json";
        $_SESSION["groupcode"] = $groupcode;
        $_SESSION["gamedir"] = $gamedir;
        $_SESSION["groupdir"] = $groupdir;
        header("Location: index.php");
        exit();
    }
    else if ($_SESSION["loginready"] == "0") {
        //data has to be written to file, so write to file and continue to this page with ready tag 1
        //get all POST data
        $restaurantname = htmlspecialchars($_POST["restname"]);
        $membernames = htmlspecialchars($_POST["membernames"]);
        //decode membernames into an array
        $membernames_array = (explode(",",$membernames));
        //get all data and read the json file
        $gamepin = $_SESSION["gamepin"];
        $gamegroup = $_SESSION["gamegroup"];
        $gamedir = "games/" . $gamepin;
        $gamegroup_post = $gamedir . "/" . $gamegroup . ".json";
        $json = file_get_contents($gamegroup_post);
        $obj = json_decode($json,true);
        //modify data
        $obj["restaurantname"] = $restaurantname;
        $obj["membernames"] = $membernames_array;
        print_r ($obj["membernames"]);
        //write data to file
        $myfile = fopen($gamegroup_post, "r+");
        $data_json = json_encode($obj);
        fwrite($myfile, $data_json);
        //redirect to this page with ready tag 1
        $_SESSION["loginready"] = "1";
        header("Location: joingame.php");
        exit();
    }
    else {
        //something happened, so destroy the session and let the user try again
        session_destroy();
        header("Location: gamepin.php");
        exit();
    }
}
else {
    //something happened, so destroy the session and let the user try again
    session_destroy();
    header("Location: gamepin.php");
    exit();
}
?>
