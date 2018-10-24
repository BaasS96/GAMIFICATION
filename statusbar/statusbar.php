<?php

    require_once("data.php");
    require_once("update.php");

    $cgame = $_GET['game'];
    $cgroup = $_GET['group'];
    $cqgroup = $_GET['qgroup'];
    $mode = $_GET['mode'];

    $games = array();
    $datamanager;

    function buildData() {
        if ($mode == "single") {
            $gpath = 'games/' . $cgame . '/group//' . cgroup . '.json';
            $cpath = 'games/' . $cgame . '/questions//' . $cqgroup;
            $datamanager = new Data($gpath, $cpath);
        } else {
            $gamefolders = scandir('games');
            foreach ($games as $value) {
                if ($game != "." && $game != "..") {
                    $game = new GameData('games/' . $game);
                    array_push($games, $game);
                }
            }
        }
    }

    header('Cache-Control: no-cache');
    header("Content-Type: text/event-stream\n\n");

    //Poll the data every 3 seconds
    while(true) {

        if ($mode == "single") {
            if ($datamanager->updaterequired) {
                $msg = json_encode(new Update($datamanager->numberofquestions, $datamanager->questionsanswered));
                echo "data: $msg" . PHP_EOL;
                echo PHP_EOL;
                ob_flush();
                flush();
            }
        } else {
            //TBD
        }

        if (connection_aborted) {
            break;
        }

        sleep(3);
    }
?>