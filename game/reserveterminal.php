<?php
    if (isset($_GET['gameid'])) {
        $game = $_GET['gameid'];
        $gid = $_GET['groupid'];
        $qgid = $_GET['qgroupid'];
        $qid = $_GET['questionid'];
        $validterminals = $_GET['validterminals'];

        $path = '../games/' . $game . '/game.json';
        $gamefile = file_get_contents($path);
        $gamefile = json_decode($gamefile);
        $terminals = $gamefile->terminals;

        if (!is_array($terminals)) {
            $terminals = (array)$terminals;
        }

        $terminalavailable = false;

        for ($i = 0; $i < count($terminals); $i++) {
            $currentterminal = $terminals[$i];
            if ($currentterminal->activated) {
                if ($currentterminal->questiongroup == $qgid && $currentterminal->question == $qid) {
                    if ($currentterminal->inuse) {
                        $terminalavailable = false;
                    } else {
                        $terminalavailable = true;
                        break;
                    }
                }
            }
        }

        if ($terminalavailable) {
            goto okay;
        } else {
            goto noterminal;
        }
    }

    okay:
    $terminal = [
        'text' => $currentterminal->text,
        'questiongroup' => $qgid,
        'question' => $qid,
        'activated' => true,
        'inuse' => true,
        'id' => $currentterminal->id
    ];
    $terminals[$i] = $terminal;
    $gamefile->terminals = $terminals;
    if (!file_put_contents($path, json_encode($gamefile))) {
        goto failure;
    }

    $groupfile = '../games/' . $game . '/g_' . $gid . '.json';
    $gfile = file_get_contents($groupfile);
    $gfile = json_decode($gfile);
    $groupterminals = (array)$gfile->terminals;
    array_push($groupterminals, $currentterminal->id);
    $gfile->terminals = $groupterminals;
    if (!file_put_contents($groupfile, json_encode($gfile))) {
        goto failure;
    }

    echo json_encode([
        "success" => true,
        "terminal" => $currentterminal->id
    ]);
    exit;

    noterminal:
    echo json_encode([
        "success" => false,
        "error" => "No terminal is available"
    ]);
    exit;

    failure:
    echo json_encode([
        "success" => false,
        "error" => "SOMETHING BAD HAPPENED!"
    ]);
    exit;
?>