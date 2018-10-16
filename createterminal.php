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
$newterminalcode = strtoupper($newterminalcode);
echo "<br />" . date("Y-m-d H:i:s") . " | Terminalcode: " . $newterminalcode;
$newterminalcode_post = $gamedir . "/" . $newterminalcode . ".json";
echo "<br />" . date("Y-m-d H:i:s") . " | Terminalcode-post: " . $newterminalcode_post;
while (in_array($newterminalcode_post, $terminals)) {
    echo "<br />" . date("Y-m-d H:i:s") . " | ERROR: FAILED";
    $newterminalcode = substr(md5(rand()), 0, 2);
    $newterminalcode = strtoupper($newterminalcode);
    echo "<br />" . date("Y-m-d H:i:s") . " | Terminalcode: " . $newterminalcode;
    $newterminalcode_post = $gamedir . "/" . $newterminalcode . ".json";
    echo "<br />" . date("Y-m-d H:i:s") . " | Terminalcode-post: " . $newterminalcode_post;
}
//create file
$filename = $gamedir . "/" . $newterminalcode . ".json";
$myfile = fopen($filename, "w");
//write data to file
$data = array("terminalcode" => $newterminalcode, "certificatenumber" => $certnum, "questionnumber" => $questnum, "idletext" => $idletext, "activated" => 0, "inuse" => 0, "groupid" => "", "qcode" => "", "exptime" => "", "qtype" => "", "question" => "", "image" => "", "answers" => array(), "ranswers" => array(), "points" => "");
$data_json = json_encode($data);
fwrite($myfile, $data_json);
fclose($myfile);
echo "<br />" . date("Y-m-d H:i:s") . " | Message: " . $newterminalcode . " was created.";
//link this terminal to the question
//get the questiondatafile
$questionfile = "games/" . $gamepin . "/questions" . "/" . $certnum . "/" . $questnum . ".json";
$questionjson = file_get_contents($questionfile);
$questiondata_new = json_decode($questionjson,true);
//modify the data
$questiondata_new["terminalid"] = $newterminalcode;
//write the modified data to the questiondatafile
$myfile = fopen($questionfile, "r+");
$data_json = json_encode($questiondata_new);
fwrite($myfile, $data_json);
fclose($myfile);
echo "<br />" . date("Y-m-d H:i:s") . " | Message: " . $newterminalcode . " was linked to question " . $certnum . "/" . $questnum . ".";
echo "<br />" . date("Y-m-d H:i:s") . " | Link: <a href='newgame.html'>Back</a>";
?>