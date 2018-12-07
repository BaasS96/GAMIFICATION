<?php
    if (isset($_GET['game'])) {
        $game = $_GET['game'];
        $terminal = $_GET['id'];

        $gamefile = '../games/' . $game . '/game.json';

        $gamedata = file_get_contents($gamefile);
        $gamedata = json_decode($gamedata);

        $o = [
            'success' => false,
            'data' => null
        ];

        $terminals = null;
        if (is_array($gamedata)) {
            $terminals = $gamedata['terminals'];
        } else {
            $terminals = $gamedata->terminals;
        }

        if (!is_array($terminals)) {
            $terminals = (array)$terminals;
        }

        for ($i = 0; $i < count($terminals); $i++) {
            if ($terminals[$i]->id == $terminal) {
               $o = [
                   'success' => true,
                   'data' => $terminals[$i]
               ];
               break;
            }
        }

        echo json_encode($o);
    }
?>