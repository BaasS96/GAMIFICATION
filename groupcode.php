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
            <form action="getgroup.php" method="post">
                <p>
                    Groepscode: <br />
                    <span class="addinfo">
                        Deze code heb je van de spelleider gekregen.<br />(Bijvoorbeeld: A9)
                    </span>
                </p>
                <input type="text" name="groupcode" class="input_text i_gc" required autofocus maxlength="2" placeholder="Groepscode">
                <input type="submit" value="GO!" class="input_submit">
                </form>
            </div>
        </div>
    </div>
</body>

</html>