<?php
//session start
session_start();
//list all folders in games
$dirs = array_filter(glob('games/*'), 'is_dir');
print_r( $dirs);
//get the gamepin
$gamepin = "games/" . $_POST["gamepin"];
print ($gamepin);
if (in_array($gamepin, $dirs)) {
    print(" : yes");
    $_SESSION["gamepin_location"] = $gamepin;
    $_SESSION["gamepin"] = $_POST["gamepin"];
    $_SESSION["gamepin_correct"] = "1";
    header("Location: groupcode.php");
    exit();
}
else {
    print(" : no");
    $_SESSION["gamepin_correct"] = "0";
    header("Location: gamepin.php");
    exit();
}
?>