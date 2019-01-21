<?php
    session_start();
    if (isset($_GET['game'])) {
        $game = $_GET['game'];
        $group = $_GET['group'];
        $_SESSION['game'] = $game;
        $_SESSION['group'] = $group;
        $_SESSION['loginexists'] = true;
    }
?>