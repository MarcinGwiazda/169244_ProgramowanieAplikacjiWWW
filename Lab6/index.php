<?php
include('cfg.php');
include('showpage.php');



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
                <li><a href="index.php?id=4">Główna strona</a></li>
                <li><a href="index.php?id=3">Najlepsze filmy</a></li>
                <li><a href="index.php?id=1">Najlepsi aktorzy pierwszoplanowi</a></li>
                <li><a href="index.php?id=6">Najlepsi reżyserzy</a></li>
                <li><a href="index.php?id=5">Kontakt</a></li>
                <li><a href="index.php?id=2">Filmiki</a></li>
            </ul>
        </nav>
    </header>
    
    <section>
        <?php
        $content = '';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $content = PokazPodstrone($id);

    if ($content && $content !== '[nie_znaleziono_strony]') {
        // Ustawia content na zawartość z bazy
    } else {
        echo "<p>Przepraszamy, strona nie istnieje.</p>";
    }
} else {
    $content = PokazPodstrone(4); // Domyślnie ładuje stronę główną o id=4
}

if ($content) {
    echo $content;
} else {
    echo "<p>Przepraszamy, strona nie istnieje.</p>";
}
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
