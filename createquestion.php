<?php
//get POST-values
if (isset($_POST["gamepin"]) && isset($_POST["questiongroup"])) {
    $gamepin = $_POST["gamepin"];
    $questiongroup = $_POST["questiongroup"];
    $gamedir = ("games/" . $gamepin . "/questions" . "/" . $questiongroup);
    $title = htmlspecialchars($_POST["title"]);
    $description = htmlspecialchars($_POST["description"]);
    $qtype = $_POST["qtype"];
    $question = htmlspecialchars($_POST["question"]);
    $image = rawurlencode($_POST["image"]);
    $answers_pre = htmlspecialchars($_POST["answers"]);
    $answers_pre2 = strtoupper($answers_pre);
    $answers = (explode(",",$answers_pre2));
    $ranswers_pre = htmlspecialchars($_POST["ranswers"]);
    $ranswers_pre2 = strtoupper($ranswers_pre);
    $ranswers = (explode(",",$ranswers_pre2));
    if (isset($_POST["useterminal"])){
        $useterminal = "true";
    }
    else {
        $useterminal = "false";
    }
}
else {
    header("Location: newgame.html");
    exit();
}
//get all existing questions within the questiongroup
$questions = array_filter(glob($gamedir . "/*"), 'is_file');
echo date("Y-m-d H:i:s") . " | Existing questions in questiongroup" . $questiongroup . ": ";
print_r( $questions);

//create random gamecode
    $num = 0;
    $newquestioncode = sprintf("%02d", $num);
    echo "<br />" . date("Y-m-d H:i:s") . " | Questioncode: " . $newquestioncode;
    $newquestioncode_post = $gamedir . "/" . $newquestioncode . ".json";
    echo "<br />" . date("Y-m-d H:i:s") . " | Questioncode-post: " . $newquestioncode_post;
    while (in_array($newquestioncode_post, $questions)) {
        echo "<br />" . date("Y-m-d H:i:s") . " | Failed";
        $num ++;
        $newquestioncode = sprintf("%02d", $num);
        echo "<br />" . date("Y-m-d H:i:s") . " | Questioncode: " . $newquestioncode;
        $newquestioncode_post = $gamedir . "/" . $newquestioncode . ".json";
        echo "<br />" . date("Y-m-d H:i:s") . " | Questioncode-post: " . $newquestioncode_post;
    }
    //create file
    $filename = $newquestioncode_post;
    echo "<br />" . date("Y-m-d H:i:s") . " | Message: " . $filename;
    $myfile = fopen($filename, "w");
    //write data to file
    $data = array("questioncode" => $newquestioncode, "questiongroup" => $questiongroup, "title" => $title, "description" => $description, "qtype" => $qtype, "question" => $question, "image" => $image, "answers" => $answers, "ranswers" => $ranswers, "useterminal" => $useterminal, "terminalid" => "");
    $data_json = json_encode($data);
    fwrite($myfile, $data_json);
    fclose($myfile);
    echo "<br />" . date("Y-m-d H:i:s") . " | Message: " . $filename . " was created.";
echo "<br />" . date("Y-m-d H:i:s") . " | Link: <a href='newgame.html'>Back</a>";
?>