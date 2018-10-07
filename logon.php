<?php 
//session start
session_start();
if (isset($_SESSION["gamepin_location"])) {
    $gamepin_location = $_SESSION["gamepin_location"];
}
else {
    header("Location: gamepin.php");
    exit();
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
            <form action="joingame.php" method="post">
                <p>
                    Groepsnaam: <br />
                    <span class="addinfo">
                        Deze naam komt op je certificaat te staan.
                    </span>
                </p>
                    <input type="text" name="groupname" class="input_text i_rn" required autofocus maxlength="256" placeholder="Groepsnaam">
                <p>
                    Namen groepsleden:<br />
                    <span class="addinfo">
                        Type na iedere naam een komma.<br />Type na de komma geen spatie.<br />(Bijvoorbeel: Henk,Mien).
                    </span>
                </p>
                    <input type="text" name="membernames" class="input_text i_mn" required maxlength="256" placeholder="Namen groepsleden">
                    <br />
                    <br />
                    <input type="submit" value="GO!" class="input_submit">
                </form>
                <?php
                    echo "<p><span class='addinfo'>Gamepin: " . $_SESSION["gamepin"] . " | Groepcode: " . $_SESSION["gamegroup"] . "</span></p>";
                ?>
            </div>
        </div>
    </div>
</body>

</html>