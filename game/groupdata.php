<?php
    if (isset($_GET['game'])) {
        $game = $_GET['game'];
        $group = $_GET['group'];
        $path = '../games/' . $game . "/g_" . $group . ".json";
        if (file_exists($path)) {
            echo json_encode(['success' => true, 'data' => file_get_contents($path)]);
        } else {
            echo json_encode(['success' => false]);
        }
    }
?>