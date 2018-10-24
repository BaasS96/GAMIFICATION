<?php

    require_once("data.php");
    require_once("update.php");

    $cgame = $_GET['game'];
    $cgroup = $_GET['group'];
    $cqgroup = $_GET['qgroup'];
    $mode = $_GET['mode'];

    header("Content-Type: text/event-stream");

    $games = array();
    $datamanager;
    $datamangers = array();
    $totalquestions = 0;
    $questionsanswered = 0;

    if ($mode == "single")  {
        $gpath = '../games/' . $cgame . '/group/' . $cgroup . '.json';
        $cpath = '../games/' . $cgame . '/questions/' . $cqgroup;
        $datamanager = new Data($gpath, $cpath, $cgroup, $cqgroup);
        $msg = json_encode(new Update($datamanager->questionsanswered, $datamanager->numberofquestions));
        echo "data: $msg" . PHP_EOL;
        echo PHP_EOL;
        ob_flush();
        flush();
    } else if ($mode == "overview") {
        $gpath = '../games/' . $cgame . '/group/' . $cgroup . '.json';
        $qs = scandir('../games/' . $cgame . '/questions');
        foreach ($q as $qs) {
            if ($q != "." && $q != ".." && is_dir($q)) {
                $cpath = '../games/' . $cgame . '/questions/' . $q;
                $data = new Data($gpath, $cpath, $cgroup, $q);
                array_push($datamanagers, $data);
                $totalquestions += $data->numberofquestions;
                $questionsanswered += $data->questionsanswered;
            }
        }
        $msg = json_encode(new Update($questionsanswered, $totalquestions));
        echo "data: $msg" . PHP_EOL;
        echo PHP_EOL;
        ob_flush();
        flush();
    } else {
        $gamefolders = scandir('games');
        foreach ($games as $value) {
            if ($game != "." && $game != "..") {
                $game = new GameData('games/' . $game);
                array_push($games, $game);
            }
        }
    }
        
    //Poll the data every 3 seconds
    while(true) {

        if ($mode == "single") {
            $datamanager->poll();
            if ($datamanager->updaterequired) {
                $msg = json_encode(new Update($datamanager->questionsanswered, $datamanager->numberofquestions));
                echo "data: $msg" . PHP_EOL;
                echo PHP_EOL;
                ob_flush();
                flush();
            }
        } else if ($mode == "overview") {
            $updaterequired = false;
            foreach($man as $datamanagers) {
                $man->poll();
                $totalquestions += $data->numberofquestions;
                $questionsanswered += $data->questionsanswered;
                $updaterequired = $man->updaterequired;
            }
            if ($updaterequired) {
                $msg = json_encode(new Update($questionsanswered, $totalquestions));
                echo "data: $msg" . PHP_EOL;
                echo PHP_EOL;
                ob_flush();
                flush();
            }
        }

        if (connection_aborted()) {
            break;
        }

        sleep(3);
    }
?>