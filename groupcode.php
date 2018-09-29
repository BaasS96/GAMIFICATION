<?php 
//session start
session_start();
if (isset($_SESSION["loginready"]) && $_SESSION["loginready"] == "2") {
    header("Location: joingame.php");
    exit();
}
if (isset($_SESSION["gamepin_location"])) {
    $gamepin_location = $_SESSION["gamepin_location"];
}
else {
    header("Location: gamepin.php");
    exit();
}
if (isset($_SESSION["gamegroup_correct"])) {
    $gamegroup_correct = $_SESSION["gamegroup_correct"];
}
else {
    $gamegroup_correct = "";
}
?>

<!DOCTYPE HTML>
<html>

<head>
    <title>
        AANMELDEN
    </title>
    <link rel="stylesheet" href="gamepin.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
</head>

<body>
    <div class="holder-v">
        <div class="holder-h">
            <div class="gamepin_input">
                <p>
                    Groepscode: <br />
                    <span class="addinfo">
                        Deze code heb je van de spelleider gekregen.<br />(Bijvoorbeeld: A9)
                    </span>
                </p>
                <?php
                    if ($gamegroup_correct == "0") {
                        echo "
                        <span class='error'>Die groepscode bestaat niet. Probeer het nog eens.</span>
                        ";
                    }
                ?>
                <form action="getgroup.php" method="post">
                    <input type="text" name="groupcode" class="input_text i_gc" required autofocus maxlength="2" placeholder="Groepscode">
                    <input type="submit" value="GO!" class="input_submit">
                </form>
            </div>
        </div>
    </div>
</body>

</html>