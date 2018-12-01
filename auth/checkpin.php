<?php
    if (isset($_GET['pin'])) {
        $pin = $_GET['pin'];
        if (file_exists('../games/'. $pin . '/game.json')) {
            $o = [
                'success' => true
            ];
        } else {
            $o = [
                'success' => false
            ];
        }
        echo json_encode($o);
    }   
?>