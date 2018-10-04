<?php
//get POST-values
if (isset($_POST["gamepin"]) && isset($_POST["certnum"]) && isset($_POST["questnum"]) && isset($_POST["idletext"])) {
    $gamepin = $_POST["gamepin"];
    $gamedir = ("games/" . $gamepin . "/terminal");
    $certnum = $_POST["certnum"];
    $questnum = $_POST["questnum"];
    $idletext = htmlspecialchars($_POST["idletext"]);
}
else {
    header("Location: newgame.html");
    exit();
}
//get all existing terminals
$terminals = array_filter(glob($gamedir . "/*"), 'is_file');
echo date("Y-m-d H:i:s") . " | Existing terminals: ";
print_r( $terminals);
//create random terminalcode
$newterminalcode = substr(md5(rand()), 0, 2);
echo "<br />" . date("Y-m-d H:i:s") . " | Terminalcode: " . $newterminalcode;
$newterminalcode_post = $gamedir . "/" . $newterminalcode . ".json";
echo "<br />" . date("Y-m-d H:i:s") . " | Terminalcode-post: " . $newterminalcode_post;
while (in_array($newterminalcode_post, $terminals)) {
    echo "<br />" . date("Y-m-d H:i:s") . " | ERROR: FAILED";
    $newterminalcode = substr(md5(rand()), 0, 2);
    echo "<br />" . date("Y-m-d H:i:s") . " | Terminalcode: " . $newterminalcode;
    $newterminalcode_post = $gamedir . "/" . $newterminalcode . ".json";
    echo "<br />" . date("Y-m-d H:i:s") . " | Terminalcode-post: " . $newterminalcode_post;
}
//create file
$filename = $gamedir . "/" . $newterminalcode . ".json";
$myfile = fopen($filename, "w");
//write data to file
$data = array("terminalcode" => $newterminalcode, "certificatenumber" => $certnum, "questionnumber" => $questnum, "idletext" => $idletext, "activated" => "0", "inuse" => "0", "groupid" => "", "qcode" => "", "exptime" => "", "qtype" => "", "question" => "", "answers" => array(""), "ranswers" => array(""), "points" => "");
$data_json = json_encode($data);
fwrite($myfile, $data_json);
fclose($myfile);
echo "<br />" . date("Y-m-d H:i:s") . " | Message: " . $newterminalcode . " was created.";
echo "<br />" . date("Y-m-d H:i:s") . " | Link: <a href='newgame.html'>Back</a>";
?>