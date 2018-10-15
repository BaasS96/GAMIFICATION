<?php
    session_start();
    if ($_SESSION["loginready"] == "2") {
        //get the gamedata file
        $gamejson = file_get_contents($_SESSION["gamedir"] . "/gamedata.json");
        $gamedata = json_decode($gamejson,true);
        //get the groupfile
        $groupjson = file_get_contents($_SESSION["groupdir"]);
        $groupdata = json_decode($groupjson,true);
        //look for certificates in the games/questions directory
        //print($_GET["qg"]);
        $questions = array_filter(glob($_SESSION["gamedir"] . "/questions" . "/" . $_GET["qg"] . "/*.json"), 'is_file');
        //print_r( $questions);
    }
    else {
        //if something is wrong, redirect to the joinpage.
        header("Location: joingame.php");
        exit();
    }
    require_once("markupsystem.php");
    //prepare the logo image src
    //decode img url string
    $decodedlogourl = urldecode($gamedata["image"]);
    if ($gamedata["imagelocation"] == "main") {
        $decodedlogourl = "images/" . $decodedlogourl;
    }
    else if ($gamedata["imagelocation"] == "game") {
        $decodedlogourl = $_SESSION["gamedir"] . "/" . "questions/" . "images/" . $decodedlogourl;
    }
    else if ($gamedata["imagelocation"] == "internet") {
        $decodedlogourl = $decodedlogourl;
    }
?>

<!DOCTYPE html>
<html>

<head>
    <title>
        <?php echo $gamedata["gamepin"] . " - " . $groupdata["groupcode"] ?>
    </title>
    <script src="functions.js"></script>
    <script src="getdata.js"></script>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="u_styles.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
</head>

<body>
    <div class="holder_top">
        <div class="top_banner">
            <div class="top_banner_resholder">
                <span class="top_banner_res"><?php echo $gamedata["grouptitle"] ?></span>
                <span class="top_banner_resname"><?php echo $groupdata["groupname"]; ?></span>
            </div>
            <div class="top_banner_title">
                <img src="<?php echo $decodedlogourl; ?>" />
                <form action="logoff.php" method="post" class="logoffform">
                    <input type="submit" class="logoffbutton" value="&#8855;" tooltip="Uitloggen">
                </form>
            </div>
        </div>
        <div class="top_stats">
            <span class="stat">Afgeronde opdrachten: <em>7</em></span>
            <span class="stat">Nog uit te voeren opdrachten: <em>0</em></span>
        </div>
    </div>
    <div class="holder">
        <?php
        foreach ($questions as $question) {
            $questionjson = file_get_contents($question);
            $questiondata = json_decode($questionjson,true);
            //print_r ( $questiongroupdata);
            //decode markup of the description
            $decodeddescription = musdecode($questiondata["description"]);
            //if question is allready answered (check if the question allready exists in the groupfile AND if points are more than 0)
            if (isset($groupdata["certificates"][$questiondata["questiongroup"]][$questiondata["questioncode"]]["points"]) && $groupdata["certificates"][$questiondata["questiongroup"]][$questiondata["questioncode"]]["points"] > "0") {
                $questiongot = "obj_certificate-YGOT";
                $gotquestion = "<em>Je hebt deze vraag al beantwoord!</em>";
            }
            else {
                $questiongot = "obj_certificate-NGOT";
            }
            echo "
            <div class='obj_certificate " . $questiongot . "'>
                <div class='obj_certificate_info'>
                    <h1>" . $questiondata["title"] . "</h1>
                    <p>" . $decodeddescription . "</p>
                    <p>
            ";
            //if the question is allready answered, display a text
            if ($questiongot == "obj_certificate-YGOT") {
                echo "<em>Je hebt deze vraag al beantwoord!</em>";
            }
            //else, display the question or the activation button for the terminal
            else if ($questiongot == "obj_certificate-NGOT") {
                if ($questiondata["useterminal"] == "false") {
                    echo "Beantwoord deze vraag: <br /><em> " . $questiondata["question"] . " </em>";
                    if ($questiondata["image"] !== "") {
                        echo "<br /><a href='" . urldecode($questiondata["image"]) . "' title='Bekijk afbeelding' target='_blank'><img src='" . urldecode($questiondata["image"]) . "' class='question_img' /></a>";
                    }
                    echo "<form action='postquestion.php' method='post'>";
                    if ($questiondata["qtype"] == "text") {
                        echo "<input type='text' class='text-input' id='qanswer' focus placeholder='Antwoord'>";
                    }
                    else if ($questiondata["qtype"] == "radio") {
                        foreach($questiondata["answers"] as $answer) {
                            echo "<input type='radio' class='radio-input' name='qanswer' id='" . $answer . "' value='" . $answer . "'><label for='" . $answer . "'>" . $answer . "</label><br />";
                        }
                    }
                    echo "<p><input type='button' class='button' onclick=\"checkQanswer();\"value='Go'></p></form>";
                }
                else if ($questiondata["useterminal"] == "true") {
                    echo "<a href='#' onclick=\"requestTerminal('" . $gamedata["gamepin"] . "','" . $gamedata["maxterminals"] . "','" . $groupdata["groupcode"] ."','" . $questiondata["questiongroup"] . "','" . $questiondata["questioncode"] . "','" . $questiondata["terminalid"] . "');\">Een terminal reserveren.</a>";
                }
            }
            echo "            
                    </p>
                </div>
            </div>
            ";
        }
        ?>
    </div>
</body>

</html>