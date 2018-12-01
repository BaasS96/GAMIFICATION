<?php
    session_start();
    if ($_SESSION['loginexists']) {
        header('Location: game.html');
    } else {
        header('Location: joingame.html');
    }
?>