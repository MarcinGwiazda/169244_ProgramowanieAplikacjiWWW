<?php
include('cfg.php'); 
include('showpage.php'); 

$title = "Strona nieznaleziona";
$content = "<p>Przepraszamy, strona nie istnieje.</p>";

if (isset($_GET['id'])) {
    $id = (int) $_GET['id']; 
    $pageData = PokazPodstrone($id);

    if (isset($pageData['content']) && $pageData['content'] !== '[nie_znaleziono_strony]') {
        $title = htmlspecialchars($pageData['title']);
        $content = $pageData['content'];
    }
} else {
    header('Location: index.php?id=1');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <meta http-equiv="Content-type" content="text/html; charset=UTF-8" />
    <meta http-equiv="Content-Language" content="pl" />
    <meta name="Author" content="Marcin Gwiazda 169244" />
    <title><?php echo $title; ?></title>
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
                <?php
                $conn = new mysqli('localhost', 'root', '', 'moja_strona');
                if ($conn->connect_error) {
                    die("Błąd połączenia z bazą danych: " . $conn->connect_error);
                }

                $query = "SELECT id, page_title FROM page_list WHERE status = 1 ORDER BY id ASC";
                $result = $conn->query($query);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<li><a href="index.php?id=' . $row['id'] . '">' . htmlspecialchars($row['page_title']) . '</a></li>';
                    }
                } else {
                    echo '<li>Brak podstron do wyświetlenia.</li>';
                }

                $conn->close();
                ?>
            </ul>
        </nav>
    </header>
    
    <main>
        <section>
            <?php echo $content; ?>
        </section>
    </main>

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
        $autor='Marcin Gwiazda'
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
