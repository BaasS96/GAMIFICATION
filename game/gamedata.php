<?php
    if (isset($_GET['game'])) {
        $game = $_GET['game'];
        $path = '../games/' . $game . "/game.json";
        if (file_exists($path)) {
            echo json_encode(['success' => true, 'data' => file_get_contents($path)]);
        } else {
            echo json_encode(['success' => false]);
        }
    }
?>