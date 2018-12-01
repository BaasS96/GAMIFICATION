<?php
    if (isset($_GET['game'])) {
        $game = $_GET['game'];
        $code = $_GET['code'];
        if (file_exists('../games/' . $game . '/g_' . $code . '.json')) {
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