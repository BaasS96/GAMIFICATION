<?php
    session_start();
    if ($_SESSION["loginready"] == "1") {
        //get the file
        $json = file_get_contents($_SESSION["groupdir"]);
        $data = json_decode($json,true);
        //convert array data into variables for easy access
        $restaurantname = $data["restaurantname"];
    }
    else {
        //if something is wrong, redirect to the joinpage.
        header("Location: joingame.php");
        exit();
    }
?>

<!DOCTYPE html>
<html>

<head>
    <title>
        Test
    </title>
    <script src="functions.js"></script>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
</head>

<body>
    <div class="holder_top">
        <div class="top_banner">
            <div class="top_banner_resholder">
                <span class="top_banner_res">Restaurant</span>
                <span class="top_banner_resname"><?php echo $restaurantname; ?></span>
            </div>
            <div class="top_banner_title">
                <img src="images/logo.png" />
                <form action="logoff.php" method="post" class="logoffform">
                    <input type="submit" class="logoffbutton" value="&#8855;" tooltip="Uitloggen">
                </form>
            </div>
        </div>
        <div class="top_stats">
            <span class="stat">Te behalen certificaten: <em>7</em></span>
            <span class="stat">Behaalde certificaten: <em>0</em></span>
        </div>
    </div>
    <div class="holder">
        <div class="obj_certificate obj_certificate-NGOT">
            <div class="obj_certificate_banner" id="c_groentefruit">
                <div class="obj_certificate_name">
                    Groente & fruit
                </div>
            </div>
            <div class="obj_certificate_info">
                <h1>Certificaat <em>Groente & fruit</em></h1>
                <p>Dit is een certificaat dat niet bestaat.</p>
            </div>
        </div>
        <div class="obj_certificate obj_certificate-NGOT">
            <div class="obj_certificate_banner" id="c_energie">
                <div class="obj_certificate_name">
                    Energie
                </div>
            </div>
            <div class="obj_certificate_info">
                <h1>Certificaat <em>Energie</em></h1>
                <p>Dit is een certificaat dat niet bestaat.</p>
            </div>
        </div>
        <div class="obj_certificate obj_certificate-NGOT">
            <div class="obj_certificate_banner" id="c_smaak">
                <div class="obj_certificate_name">
                    Smaak
                </div>
            </div>
            <div class="obj_certificate_info">
                <h1>Certificaat <em>Smaak</em></h1>
                <p>Dit is een certificaat dat niet bestaat.</p>
            </div>
        </div>
        <div class="obj_certificate obj_certificate-NGOT">
            <div class="obj_certificate_banner" id="c_schijfvanvijf">
                <div class="obj_certificate_name">
                    Schijf van vijf
                </div>
            </div>
            <div class="obj_certificate_info">
                <h1>Certificaat <em>Schijf van vijf</em></h1>
                <p>Dit is een certificaat dat niet bestaat.</p>
            </div>
        </div>
        <div class="obj_certificate obj_certificate-NGOT">
            <div class="obj_certificate_banner" id="c_suiker">
                <div class="obj_certificate_name">
                    Suiker
                </div>
            </div>
            <div class="obj_certificate_info">
                <h1>Certificaat <em>Suiker</em></h1>
                <p>Dit is een certificaat dat niet bestaat.</p>
            </div>
        </div>
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
    </div>
    <!--
    <input type="text" id="codeInput1">
    <input type="button" id="codeButton1" onclick="enterCode1()" value="GO">
    <div id="feedback1">
    </div>
-->
</body>

</html>