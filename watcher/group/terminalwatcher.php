<?php

    include_once('../TerminalData.php');

    $cgame = $_GET['game'];
    $cgroup = $_GET['group'];
    $cqgroup = $_GET['qgroup'];
    $terminalid = explode(',', $_GET['terminals']);

    header("Content-Type: text/event-stream");

    $terminals = array();

    $update = [
        "numofterminals" => 0,
        "terminaldata" => []
    ];

    while(true) {
        $terminals = array();
        $update["numofterminals"] = 0;
        $update["terminaldata"] = [];
        foreach ($terminalid as $terminal) {
            $path = '../../games/' . $cgame . '/terminal/' . $terminal . '.json';
            $terminalc = new TerminalData(new Type(Type::FILE), $path, $cgame, $cgroup, $terminal);
            if ($terminalc->inuse) {
                array_push($terminals, $terminalc);
                $update["numofterminals"]++;
                $$terminal = [
                    "terminalid" => $terminalc->getTerminalID(),
                    "qgroup" => $terminalc->getQGroup(),
                    "qcode" => $terminalc->getQCode(),
                    "timeleft" => $terminalc->getTimeLeft(),
                    "inuse" => $terminalc->getUseStatus()
                ];
                array_push($update["terminaldata"], $$terminal);
            } else {
                array_splice($terminalid, array_search($terminal, $terminalid));
            }
        }

        $s = json_encode($update);

        echo "data: $s" . PHP_EOL;
        echo PHP_EOL;
        ob_flush();
        flush();

        if (connection_aborted()) {
            break;
        }

        sleep(2);

    }

?>