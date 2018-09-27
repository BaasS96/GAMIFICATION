<?php
//get POST-values
if (isset($_POST["gamepin"]) && isset($_POST["groupnum"])) {
    $gamepin = $_POST["gamepin"];
    $gamedir = ("games/" . $gamepin);
    $groupnum = $_POST["groupnum"];
}
else {
    header("Location: newgame.html");
    exit();
}
//get all existing groups
$groups = array_filter(glob($gamedir), 'is_file');
echo date("Y-m-d H:i:s") . " | Existing games: ";
print_r( $groups);
//create random gamecode
$newgroupcode = substr(md5(rand()), 0, 2);
echo "<br />" . date("Y-m-d H:i:s") . " | Groupcode: " . $newgroupcode;
/*
//add dir to gamecode
$newgamecode_post = "games/" . $newgamecode;
//if gamecode is in use, create another
while (in_array($newgamecode_post, $groups)) {
    echo "<br />" . date("Y-m-d H:i:s") . " | ERROR: FAILED";
    $newgamecode = mt_rand(0,9) . mt_rand(0,9) . mt_rand(0,9) . mt_rand(0,9);
    echo "<br />" . date("Y-m-d H:i:s") . " | Gamecode: " . $newgamecode;
    $newgamecode_post = "games/" . $newgamecode;
}
    //create gamedir
    mkdir($newgamecode_post, 0777, TRUE);
    echo "<br />" . date("Y-m-d H:i:s") . " | Message: " . $newgamecode_post . " was created.";
    echo "<br />" . date("Y-m-d H:i:s") . " | Message: You can now use game #" . $newgamecode;
    echo "<br />" . date("Y-m-d H:i:s") . " | Link: <a href='newgame.html'>Back</a>";
*/
?>