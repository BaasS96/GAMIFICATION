<?php
//get POST-values
if (isset($_POST["gamepin"]) && isset($_POST["groupnum"])) {
    $gamepin = $_POST["gamepin"];
    $gamedir = ("games/" . $gamepin . "/group");
    $groupnum = $_POST["groupnum"];
}
else {
    header("Location: newgame.html");
    exit();
}
//get all existing groups
$groups = array_filter(glob($gamedir . "/*"), 'is_file');
echo date("Y-m-d H:i:s") . " | Existing groups: ";
print_r( $groups);
//create random gamecode
$i = 0;
while ($i < $groupnum) {
    $newgroupcode = substr(md5(rand()), 0, 2);
    $newgroupcode = strtoupper($newgroupcode);
    echo "<br />" . date("Y-m-d H:i:s") . " | Groupcode: " . $newgroupcode;
    $newgroupcode_post = $gamedir . "/" . $newgroupcode . ".json";
    echo "<br />" . date("Y-m-d H:i:s") . " | Groupcode-post: " . $newgroupcode_post;
    while (in_array($newgroupcode_post, $groups)) {
        echo "<br />" . date("Y-m-d H:i:s") . " | ERROR: FAILED";
        $newgroupcode = substr(md5(rand()), 0, 2);
        $newgroupcode = strtoupper($newgroupcode);
        echo "<br />" . date("Y-m-d H:i:s") . " | Groupcode: " . $newgroupcode;
        $newgroupcode_post = $gamedir . "/" . $newgroupcode . ".json";
        echo "<br />" . date("Y-m-d H:i:s") . " | Groupcode-post: " . $newgroupcode_post;
    }
    //create file
    $filename = $gamedir . "/" . $newgroupcode . ".json";
    $myfile = fopen($filename, "w");
    //write data to file
    $data = array("groupcode" => $newgroupcode, "groupname" => "", "membernames" => array(), "certificates" => array(), "terminals" => array(), "lastactive" => "");
    $data_json = json_encode($data);
    fwrite($myfile, $data_json);
    fclose($myfile);
    echo "<br />" . date("Y-m-d H:i:s") . " | Message: " . $newgroupcode . " was created.";
    $i ++;
} 
echo "<br />" . date("Y-m-d H:i:s") . " | Link: <a href='newgame.html'>Back</a>";
?>