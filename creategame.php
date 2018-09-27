<?php
//get all existing games
$dirs = array_filter(glob('games/*'), 'is_dir');
echo date("Y-m-d H:i:s") . " | Existing games: ";
print_r( $dirs);
//create random gamecode
$newgamecode = mt_rand(0,9) . mt_rand(0,9) . mt_rand(0,9) . mt_rand(0,9);
echo "<br />" . date("Y-m-d H:i:s") . " | Gamecode: " . $newgamecode;
//add dir to gamecode
$newgamecode_post = "games/" . $newgamecode;
//if gamecode is in use, create another
while (in_array($newgamecode_post, $dirs)) {
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
?>