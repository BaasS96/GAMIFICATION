<?php 
//session start
session_start();
if (isset($_SESSION["loginready"]) && $_SESSION["loginready"] == "2") {
    header("Location: joingame.php");
    exit();
}
if (isset($_SESSION["gamepin_correct"])) {
    $gamepin_correct = $_SESSION["gamepin_correct"];
}
else {
    $gamepin_correct = "";
}
?>

<!DOCTYPE HTML>
<html>

<head>
    <title>
        GAMEPIN
    </title>
    <link rel="stylesheet" href="gamepin.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
</head>

<body>
    <div class="holder-v">
        <div class="holder-h">
            <div class="gamepin_input">
                <p>Welkom! Voer je gamepin in:</p>
                <?php
                    if ($gamepin_correct == "0") {
                        echo "
                        <span class='error'>Die gamepin bestaat niet. Probeer het nog eens.</span>
                        ";
                    }
                ?>
                <form action="getgame.php" method="post">
                    <input type="text" length="4" name="gamepin" class="input_text i_gp" required autofocus maxlength="4" placeholder="GAMEPIN">
                    <input type="submit" value="GO!" class="input_submit">
                </form>
            </div>
        </div>
    </div>
</body>

</html>