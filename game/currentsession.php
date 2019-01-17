<?php
    session_start();
    if (!isset($_SESSION['loginexists'])) {
        echo "{\"error\": true}";
        exit;
    }
    if ($_SESSION['loginexists']) {
        $o = [
            'game' => $_SESSION['game'],
            'group' => $_SESSION['group']
        ];
        echo json_encode($o);
    }
?>