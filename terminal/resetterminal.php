<?php
    $entityBody = file_get_contents('php://input');

    $basepath = "../games/" . $entityBody->game;
    $terminalpath = $basepath . "/game.json";
    $grouppath = $basepath . "/g_" . $entityBody->group . ".json";

    $terminaldata = file_get_contents($terminalpath);
    $terminaldata = json_decode($terminaldata);

    $currentterminal = null;
    $index = 0;
    
    for ($i = 0; $i < count($terminaldata->terminals); $i++) {
        if ($terminaldata->terminals[$i]->id == $entityBody->terminal) {
            $currentterminal = $terminaldata->terminals[$i];
            $index = $i;
            break;
        }
    }

    $terminal = [
        'text' => $currentterminal->text,
        'questiongroup' => NULL,
        'question' => NULL,
        'group' => NULL,
        'activated' => true,
        'inuse' => false,
        'id' => $currentterminal->id
    ];
    $terminaldata->terminals[$index] = $terminal;
    $newdata = json_encode($terminaldata);
    if (!file_put_contents($terminalpath, $newdata)) {
        goto failure;
    }

    $groupdata = file_get_contents($grouppath);
    $groupdata = json_decode($groupdata);

    $groupdata->terminals = array_diff((array)$groupdata->terminals, [$entityBody->terminal]);

    $newdata = json_encode($groupdata);
    if (!file_put_contents($grouppath, $newdata)) {
        goto failure;
    }

    echo json_encode(["succes" => true]);
    exit;

    failure:
    echo json_encode([
        "succes" => false,
        "error" => "SOMETHING BAD HAPPENED!"
    ]);
    exit;
?>