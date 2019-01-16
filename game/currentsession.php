<?php
    session_start();
    if ($_SESSION['loginexists']) {
        $o = [
            'game' => $_SESSION['game'],
            'group' => $_SESSION['group']
        ];
        echo json_encode($o);
    }
?>