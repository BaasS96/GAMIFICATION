<?php
    if (isset($_GET['game'])) {
        $game = $_GET['game'];
        $path = '../games/' . $game;
        $dir = scandir($path);
        $groups = [];
        foreach ($dir as $value) {
            if (strpos($value, "g_") !== false) {
                $gid = substr($value, 0, strlen($value) - 5);
                $parts = explode("_", $gid);
                $groups[$parts[1]] = file_get_contents($path . '/' . $value);
            }
        }
        echo json_encode($groups);
    }
?>