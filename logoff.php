<?php
    session_start();
    session_destroy();
    header("Location: gamepin.php");
    exit();
?>