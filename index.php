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
        $questiongroups = array_filter(glob($_SESSION["gamedir"] . "/questions/*.json"), 'is_file');
        //print_r( $questiongroups);
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
        foreach ($questiongroups as $questiongroup) {
            $questiongroupjson = file_get_contents($questiongroup);
            $questiongroupdata = json_decode($questiongroupjson,true);
            //print_r ( $questiongroupdata);
            //decode img url string
            $decodedimageurl = urldecode($questiongroupdata["image"]);
            if ($questiongroupdata["imagelocation"] == "main") {
                $decodedimageurl = "images/" . $decodedimageurl;
            }
            else if ($questiongroupdata["imagelocation"] == "game") {
                $decodedimageurl = $_SESSION["gamedir"] . "/" . "questions/" . "images/" . $decodedimageurl;
            }
            else if ($questiongroupdata["imagelocation"] == "internet") {
                $decodedimageurl = $decodedimageurl;
            }
            //decode markup of the description
            $decodeddescription = musdecode($questiongroupdata["description"]);
            echo "
            <div class='obj_certificate pointerhand obj_certificate-NGOT' onclick=\"toQuestionGroup('" . $questiongroupdata["questiongroupid"] . "');\" title='Naar vragengroep " . $questiongroupdata["name"] . "'>
                <div class='obj_certificate_banner' id='qg_" . $questiongroupdata["questiongroupid"] . "' style='background-image: url(\"" . $decodedimageurl . "\");'>
                    <div class='obj_certificate_name'>
                        " . $questiongroupdata["name"] . "
                    </div>
                </div>
                <div class='obj_certificate_info'>
                    <h1>" . $questiongroupdata["longname"] . "</h1>
                    <p>" . $decodeddescription . "</p>
                </div>
            </div>
            ";
        }
        ?>
        <!--
        <div class="obj_certificate obj_certificate-YGOT">
            <div class="obj_certificate_banner" id="c_gezondongezond">
                <div class="obj_certificate_check">
                    &#10003;
                </div>
                <div class="obj_certificate_name">
                    Gezond & ongezond
                </div>
            </div>
            <div class="obj_certificate_info">
                <h1>Certificaat <em>Gezond & ongezond</em></h1>
                <p>Dit is een certificaat dat niet bestaat.</p>
            </div>
        </div>
        <div class="obj_certificate obj_certificate-NGOT">
            <div class="obj_certificate_banner" id="c_eetcultuur">
                <div class="obj_certificate_name">
                    Eetcultuur
                </div>
            </div>
            <div class="obj_certificate_info">
                <h1>Certificaat <em>Eetcultuur</em></h1>
                <p>Dit is een certificaat dat niet bestaat.</p>
            </div>
        </div>
        !-->
    </div>
</body>

</html>