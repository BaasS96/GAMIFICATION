<?php
    session_start();
    if (isset($_GET['game'])) {
        $game = $_GET['game'];
        $group = $_GET['group'];
        $name = $_GET['name'];
        $members = $_GET['members'];
        $filepath = '../games/' . $game . '/g_' . $group . '.json';
        $old = file_get_contents($filepath);
        $old = json_decode($old);
        $old->name = $name;
        $old->members = $members;
        if (file_put_contents($filepath, json_encode($old))) {
            $o = [
                'success' => true
            ];
            $_SESSION['game'] = $game;
            $_SESSION['group'] = $group;
            $_SESSION['loginexists'] = true;
        } else {
            $o = [
                'success' => false
            ];
        }
        echo json_encode($o);
    }
?>