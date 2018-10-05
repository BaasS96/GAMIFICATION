<?php
//get POST-values
if (isset($_POST["gamepin"]) && isset($_POST["name"])) {
    $gamepin = $_POST["gamepin"];
    $gamedir = ("games/" . $gamepin);
    $name = $_POST["name"];
    $longname = $_POST["longname"];
    $description = $_POST["description"];
    $image = rawurlencode($_POST["image"]);
}
else {
    header("Location: newgame.html");
    exit();
}
//get all existing groups
$groups = array_filter(glob($gamedir . "/questions/*"), 'is_dir');
echo date("Y-m-d H:i:s") . " | Existing questiongroups: ";
print_r( $groups);

//create random gamecode
    $num = 0;
    $newgroupcode = sprintf("%02d", $num);
    echo "<br />" . date("Y-m-d H:i:s") . " | Questiongroup-id: " . $newgroupcode;
    $newgroupcode_post = $gamedir . "/questions" . "/" . $newgroupcode;
    echo "<br />" . date("Y-m-d H:i:s") . " | Questiongroup-id-post: " . $newgroupcode_post;
    while (in_array($newgroupcode_post, $groups)) {
        echo "<br />" . date("Y-m-d H:i:s") . " | Failed";
        $num ++;
        $newgroupcode = sprintf("%02d", $num);
        echo "<br />" . date("Y-m-d H:i:s") . " | Questiongroup-id: " . $newgroupcode;
        $newgroupcode_post = $gamedir . "/questions" . "/" . $newgroupcode;
        echo "<br />" . date("Y-m-d H:i:s") . " | Questiongroup-id-post: " . $newgroupcode_post;
    }
    //create dir
    mkdir($newgroupcode_post, 0777, TRUE);
    echo "<br />" . date("Y-m-d H:i:s") . " | Message: " . $newgroupcode_post . " was created.";
    //create file
    $filename = $newgroupcode_post . ".json";
    echo "<br />" . date("Y-m-d H:i:s") . " | Message: " . $filename;
    $myfile = fopen($filename, "w");
    //write data to file
    $data = array("questiongroupid" => $newgroupcode, "name" => $name, "longname" => $longname, "description" => $description, "image" => $image);
    $data_json = json_encode($data);
    fwrite($myfile, $data_json);
    fclose($myfile);
    echo "<br />" . date("Y-m-d H:i:s") . " | Message: " . $filename . " was created.";

echo "<br />" . date("Y-m-d H:i:s") . " | Link: <a href='newgame.html'>Back</a>";
?>