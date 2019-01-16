<?php
//session start
session_start();
//list all groups
$gamepin = $_SESSION["gamepin"];
$gamegroup = $newgroupcode = strtoupper($_POST["groupcode"]);
$gamedir = "games/" . $gamepin . "/group";
$gamegroup_post = $gamedir . "/" . $gamegroup . ".json";
//get all existing groups
$groups = array_filter(glob($gamedir . "/*"), 'is_file');
print_r( $groups);
if (in_array($gamegroup_post, $groups)) {
    print(" : yes");
    $_SESSION["gamegroup_correct"] = "1";
    $_SESSION["gamegroup"] = $gamegroup;
    //get data from json file
    $json = file_get_contents($gamegroup_post);
    $obj = json_decode($json,true);
    print_r( $obj);
    if (!empty($obj["groupname"]) && !empty($obj["membernames"])) {
        $_SESSION["loginready"] = "1";
        header("Location: joingame.php");
        exit();
    }
    else {
        $_SESSION["loginready"] = "0";
        header("Location: logon.php");
        exit();
    }
}
else {
    print(" : no");
    $_SESSION["gamegroup_correct"] = "0";
    header("Location: groupcode.php");
    exit();
}
?>