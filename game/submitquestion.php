<?php
    //Groupid, gameid, qgroup, question, answerdata (correct, points, timeleft, given answer)
    $entityBody = file_get_contents('php://input');
    $entityBody = json_decode($entityBody);
    header('Content-Type:application/json');
    $groupfile = "../games/" . $entityBody->game . "/g_" . $entityBody->group . ".json";
    $groupjson = file_get_contents($groupfile);
    $groupdata = json_decode($groupjson,true);

    $answer = $entityBody->answerdata;
    $groupdata["certificates"][$entityBody->qgroup][$entityBody->question]["answer"] = $answer->answer;
    $groupdata["certificates"][$entityBody->qgroup][$entityBody->question]["correct"] = $answer->correct;
    $groupdata["certificates"][$entityBody->qgroup][$entityBody->question]["points"] = $answer->points;
    $groupdata["certificates"][$entityBody->qgroup][$entityBody->question]["timeleft"] = $answer->timeleft;
    $groupdata["lastactive"] = time();

    $newdata = json_encode($groupdata);

    echo json_encode(["succes" => file_put_contents($groupfile, $newdata)]);
?>