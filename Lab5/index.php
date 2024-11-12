<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);

$strona = 'html/glowna.html';

if ($_GET['idp'] == '') {
    $strona = 'html/glowna.html';
} elseif ($_GET['idp'] == 'glowna') {
    $strona = 'html/glowna.html';
} elseif ($_GET['idp'] == 'filmy') {
    $strona = 'html/filmy.html';
} elseif ($_GET['idp'] == 'aktorzy') {
    $strona = 'html/aktorzy.html';
} elseif ($_GET['idp'] == 'rezyserzy') {
    $strona = 'html/rezyserzy.html';
} elseif ($_GET['idp'] == 'kontakt') {
    $strona = 'html/kontakt.html';
	} elseif ($_GET['idp'] == 'filmiki') {
    $strona = 'html/filmiki.html';
} else {
    echo "<p>Strona nie została znaleziona.</p>";
    exit; 
}
if (!file_exists($strona)) {
    echo "<p>Wybrana strona nie istnieje lub nie można jej załadować.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
    <meta http-equiv="Content-Language" content="pl" />
    <meta name="Author" content="Marcin Gwiazda 169244" />
    <title>Filmy oscarowe</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/kolorujtlo.js" type="text/javascript"></script>
    <script src="js/timedate.js" type="text/javascript"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body onload="startclock()">
    <header>
        <div style="text-align: right; padding: 10px;">
            <div id="zegarek"></div>
            <div id="data"></div>
        </div>
        <h1><b><i><u>Filmy oscarowe</b></i></u></h1>
        <nav>
            <ul>
                <li><a href="index.php?idp=glowna">Główna strona</a></li>
                <li><a href="index.php?idp=filmy">Najlepsze filmy</a></li>
                <li><a href="index.php?idp=aktorzy">Najlepsi aktorzy pierwszoplanowi</a></li>
                <li><a href="index.php?idp=rezyserzy">Najlepsi reżyserzy</a></li>
                <li><a href="index.php?idp=kontakt">Kontakt</a></li>
                <li><a href="index.php?idp=filmiki">Filmiki</a></li>
            </ul>
        </nav>
    </header>
    
    <section>
        <?php
        include($strona);
        ?>
    </section>

    <section>
        <div style="float: left; margin-left: 20px;">
            <form method="post" name="background">
                <input type="button" value="Biały" onclick="changeBackground('#FFFFFF')" class="color-button">
                <input type="button" value="Złoty" onclick="changeBackground('#FFD700')" class="color-button">
                <input type="button" value="Bordowy" onclick="changeBackground('#800000')" class="color-button">
            </form>
        </div>
    </section>

    <footer>
        <?php
        $nr_indeksu = 169244;
        $nrGrupy = '3';
        echo "Autor: Marcin Gwiazda $nr_indeksu grupa $nrGrupy <br><br>";
        ?>
    </footer>

    <script>
        $(".color-button").on({
            "mouseover": function() {
                $(this).animate({ width: "+=10px", height: "+=5px" }, 300);
            },
            "mouseout": function() {
                $(this).animate({ width: "-=10px", height: "-=5px" }, 300);
            }
        });

        $("img").on("click", function() {
            if (!$(this).is(":animated")) {
                $(this).animate({ width: "+=50px", height: "+=50px" }, { duration: 2000 });
            }
        });
    </script>
</body>
</html>
