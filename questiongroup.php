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
        $questions = array_filter(glob($_SESSION["gamedir"] . "/questions" . "/" . $_GET["qg"] . "/*.json"), 'is_file');
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
    if ($gamedata["image"] == "logo") {
        //random
        $num = mt_rand(1,4);
        $decodedlogourl = urldecode("logo" . $num . ".png");
    }
    if ($gamedata["imagelocation"] == "main") {
        $decodedlogourl = "images/" . $decodedlogourl;
    }
    else if ($gamedata["imagelocation"] == "game") {
        $decodedlogourl = $_SESSION["gamedir"] . "/" . "questions/" . "images/" . $decodedlogourl;
    }
    else if ($gamedata["imagelocation"] == "internet") {
        $decodedlogourl = $decodedlogourl;
    }
    if ($gamedata["image"] == "logo") {
        //random
        $num = mt_rand(1,4);
        $decodedlogourl = urldecode("images/logo" . $num . ".png");
    }
    //get questiongroupdata
    $questiongroupfile = $_SESSION["gamedir"] . "/questions" . "/" . $_GET["qg"] . ".json";
    $questiongroupjson = file_get_contents($questiongroupfile);
    $questiongroupdata = json_decode($questiongroupjson,true);
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
    <link rel="stylesheet" href="style_aux.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>

<?php echo "<body onload=\"loadStats('" . $gamedata["gamepin"] . "','" . $groupdata["groupcode"] . "','" . $_GET["qg"] . "','single');\">"; ?>
    <!--
        WIP Terminal monitor
    <div class="terminal_monitor">
        <div class="terminal_monitor_header">
            1 Actieve Terminal
        </div>
        <div class="terminal_monitor_element">
            <div class="terminal_monitor_element_component">
                <span>Vraag #0</span>
                <span>IDK?</span>    
            </div>
            <div class="terminal_monitor_element_component">
                <span>02:09</span>
            </div>
        </div>
        <div class="terminal_monitor_element">
            <div class="terminal_monitor_element_component">
                <span>Vraag #0</span>
                <span>IDK?</span>    
            </div>
            <div class="terminal_monitor_element_component">
                <span>02:09</span>
            </div>
        </div>
    </div>
    !-->
    <div class="holder_top">
        <div class="top_banner">
            <div class="top_banner_resholder">
                <span class="top_banner_res"><?php echo $gamedata["grouptitle"] ?></span>
                <span class="top_banner_resname"><?php echo $groupdata["groupname"]; ?></span>
            </div>
            <div class="top_banner_title">
                <img src="<?php echo $decodedlogourl; ?>" />
                <form action="logoff.php" method="post" class="logoffform">
                    <button class="logoffbutton" tooltip="Uitloggen"><i class='material-icons'>power_settings_new</i></button>
                </form>
            </div>
        </div>
        <div class="top_stats">
            <span class="stat">Totaal aantal opdrachten: <span id="stat_q_total">N/A</span></span>
            <span class="stat">Afgeronde opdrachten: <span id="stat_q_done">N/A</span></span>
        </div>
    </div>
    <div class="holder">
        <div class="breadcrumbs_holder"><a class="breadcrumbs" title="Terug naar het overzicht." href="index.php" target="_self">Game <em><?php echo($gamedata["gamepin"]); ?></em></a> - Vragengroep <em><?php echo($questiongroupdata["name"]); ?></em></div>
        <?php
        foreach ($questions as $question) {
            $questionjson = file_get_contents($question);
            $questiondata = json_decode($questionjson,true);
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
                    <div id='question_" . $questiondata["questioncode"] . "'>
                    <p class='question_divided'>
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
                    echo "</p><form action='postquestion.php' method='post'>";
                    if ($questiondata["qtype"] == "text") {
                        echo "<input type='text' class='text-input' id='qanswer_" . $questiondata["questioncode"] . "' focus placeholder='Antwoord'>";
                    }
                    else if ($questiondata["qtype"] == "radio") {
                        foreach($questiondata["answers"] as $answer) {
                            echo "<input type='radio' class='radio-input' name='qanswer_" . $questiondata["questioncode"] . "' id='q_" . $questiondata["questioncode"] . "-" . $answer . "' value='" . $answer . "'><label for='q_" . $questiondata["questioncode"] . "-" . $answer . "'>" . $answer . "</label><br />";
                        }
                    }
                    echo "<p><input type='button' class='input_submit' onclick=\"checkQanswer('" . $gamedata["gamepin"] . "','" . $questiondata["questiongroup"] . "','" . $questiondata["questioncode"] . "','" . $groupdata["groupcode"] . "');\"value='Go'></p></form>";
                }
                else if ($questiondata["useterminal"] == "true") {
                    echo "Om deze vraag te beantwoorden moet je een terminal reserveren. </p><p id='feedbackholder_" . $questiondata["questiongroup"] . "-" . $questiondata["questioncode"] . "'><button class='input_submit' onclick=\"requestTerminal('" . $gamedata["gamepin"] . "','" . $gamedata["maxterminals"] . "','" . $groupdata["groupcode"] ."','" . $questiondata["questiongroup"] . "','" . $questiondata["questioncode"] . "','" . $questiondata["terminalid"] . "','" . $questiondata["qcode"] . "','" . $questiondata["timetillexp"] . "');\">Reserveer een terminal</button></p>";
                }
            }
            echo "            
                    </div>
                    <p class='feedback' id='questionfeedback_" . $questiondata["questioncode"] . "'></p>
                </div>
            </div>
            ";
        }
        ?>
    </div>
</body>

</html>