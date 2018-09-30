<?php
session_start();
if (!isset($_SESSION["terminalid"]) || !isset($_SESSION["gamepin"]) || !isset($_SESSION["sessionactive"]) || $_SESSION["sessionactive"] != "1") {
    header("Location: terminalenter.php");
    exit();
}
if (isset($_GET["deactivate"]) && $_GET["deactivate"] == "1b") {
    //to fully deactivate the terminal, type ?deactivate=1b behind the adress of this web page and press enter
    //get terminaldata
    $terminaljson = file_get_contents($_SESSION["terminaldir"]);
    $terminaldata = json_decode($terminaljson,true);
    //register the terminal as deactivated
    $terminaldata["activated"] = "0";
    //write the modified data to the terminal file
    $myfile = fopen($_SESSION["terminaldir"], "r+");
    $data_json = json_encode($terminaldata);
    fwrite($myfile, $data_json);
    fclose($myfile);
    //unset session and destroy
    session_unset(); 
    session_destroy();
    //return to the terminalenter page
    header("Location: terminalenter.php");
    exit();
}
//get terminaldata
$terminaljson = file_get_contents($_SESSION["terminaldir"]);
$terminaldata = json_decode($terminaljson,true);
?>

<!DOCTYPE html>
<html>
    <head>
        <title>
            Terminal
        </title>
        <link rel="stylesheet" href="terminal.css">
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    </head>
    <body>
        <div class="holder-v">
            <div class="holder-h">
                <div class="holder">
                    <?php echo "<p class='idletext'>" . $terminaldata["idletext"] . "</p>"; ?>
                </div>
            </div>
        </div>
    </body>
</html>