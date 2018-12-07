<?php
    //get POST-values
    if (isset($_POST["creatorname"])) {
        $creator = htmlspecialchars($_POST["creatorname"]);
        $grouptitle = htmlspecialchars($_POST["grouptitle"]);
        $image = rawurlencode($_POST["image"]);
        $imagelocation = $_POST["image_location"];
        $maxterminals = $_POST["maxterminals"];
        $crtime = time();
    }
    else {
        header("Location: newgame.html");
        exit();
    }
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
    //create terminaldir
    $terminaldir = $newgamecode_post . "/terminal";
    mkdir($terminaldir, 0777, TRUE);
    echo "<br />" . date("Y-m-d H:i:s") . " | Message: " . $terminaldir . " was created.";
    //create groupdir
    $groupdir = $newgamecode_post . "/group";
    mkdir($groupdir, 0777, TRUE);
    echo "<br />" . date("Y-m-d H:i:s") . " | Message: " . $groupdir . " was created.";
    //create questionsdir
    $questionsdir = $newgamecode_post . "/questions";
    mkdir($questionsdir, 0777, TRUE);
    echo "<br />" . date("Y-m-d H:i:s") . " | Message: " . $questionsdir . " was created.";
    //create questionsdir imagesdir
    $questionsdir_img = $newgamecode_post . "/questions" . "/images";
    mkdir($questionsdir_img, 0777, TRUE);
    echo "<br />" . date("Y-m-d H:i:s") . " | Message: " . $questionsdir_img . " was created.";
    //create gamedata file
    $filename = $newgamecode_post . "/gamedata.json";
    echo "<br />" . date("Y-m-d H:i:s") . " | Message: " . $filename;
    $myfile = fopen($filename, "w");
    //write data to file
    $data = array("gamepin" => $newgamecode, "creatorname" => $creator, "grouptitle" => $grouptitle, "crtime" => $crtime, "image" => $image, "imagelocation" => $imagelocation, "maxterminals" => $maxterminals);
    $data_json = json_encode($data);
    fwrite($myfile, $data_json);
    fclose($myfile);
    echo "<br />" . date("Y-m-d H:i:s") . " | Message: " . $filename . " was created.";
    //READY
    echo "<br />" . date("Y-m-d H:i:s") . " | READY";
    echo "<br />" . date("Y-m-d H:i:s") . " | Message: You can now use game #" . $newgamecode;
    echo "<br />" . date("Y-m-d H:i:s") . " | Link: <a href='newgame.html#creategame'>Back</a>";
?>  