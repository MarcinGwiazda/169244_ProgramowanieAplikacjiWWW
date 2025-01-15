<?php
/**
  Główna logika strony
  Ładuje treść podstrony na podstawie jej ID z bazy danych.
  Domyślnie wyświetla stronę główną lub komunikat o braku podstrony.
 */

// Dołączenie plików konfiguracyjnych i funkcji
include('cfg.php'); 
include('showpage.php'); 

// Domyślne wartości dla strony nieznalezionej
$title = "Strona nieznaleziona";
$content = "<p>Przepraszamy, strona nie istnieje.</p>";

/**
  Pobieranie treści podstrony na podstawie ID
  Zabezpieczenie przed SQL Injection
 */
if (isset($_GET['id'])) {
    $id = (int) $_GET['id']; // Konwersja na int w celu zabezpieczenia
    $pageData = PokazPodstrone($id); // Pobiera dane podstrony (tytuł i treść)

    if (isset($pageData['content']) && $pageData['content'] !== '[nie_znaleziono_strony]') {
        $title = htmlspecialchars($pageData['title']); // Escapuje znaki HTML
        $content = $pageData['content'];
    }
} else {
    // Przekierowanie na stronę główną, jeśli brak ID w GET
    header('Location: index.php?id=1');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pl">
<head>
    <!-- Sekcja meta i stylów -->
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
        <!-- Sekcja zegarka -->
        <div style="text-align: right; padding: 10px;">
            <div id="zegarek"></div>
            <div id="data"></div>
        </div>

        <!-- Tytuł strony -->
        <h1><b><i><u>Filmy oscarowe</b></i></u></h1>

        <!-- Nawigacja dynamiczna -->
        <nav>
            <ul>
                <?php
                // Połączenie z bazą danych i generowanie linków nawigacyjnych
                $conn = new mysqli('localhost', 'root', '', 'moja_strona');
                if ($conn->connect_error) {
                    die("Błąd połączenia z bazą danych: " . $conn->connect_error);
                }

                $query = "SELECT id, page_title FROM page_list WHERE status = 1 ORDER BY id ASC"; // Zapytanie z parametrem LIMIT
                $result = $conn->query($query);

                // Generowanie elementów nawigacji
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<li><a href="index.php?id=' . $row['id'] . '">' . htmlspecialchars($row['page_title']) . '</a></li>';
                    }
                } else {
                    echo '<li>Brak podstron do wyświetlenia.</li>';
                }

                $conn->close(); // Zamknięcie połączenia z bazą danych
                ?>
            </ul>
        </nav>
    </header>
    
    <!-- Sekcja główna -->
    <main>
        <section>
            <?php echo $content; ?>
        </section>
    </main>

    <!-- Sekcja zmiany tła -->
    <section>
        <div style="float: left; margin-left: 20px;">
            <form method="post" name="background">
                <input type="button" value="Biały" onclick="changeBackground('#FFFFFF')" class="color-button">
                <input type="button" value="Złoty" onclick="changeBackground('#FFD700')" class="color-button">
                <input type="button" value="Bordowy" onclick="changeBackground('#800000')" class="color-button">
            </form>
        </div>
    </section>

   

    <!-- Skrypty JavaScript -->
    <script>
        // Animacje przycisków
        $(".color-button").on({
            "mouseover": function() {
                $(this).animate({ width: "+=10px", height: "+=5px" }, 300);
            },
            "mouseout": function() {
                $(this).animate({ width: "-=10px", height: "-=5px" }, 300);
            }
        });

        // Animacje obrazków
        $("img").on("click", function() {
            if (!$(this).is(":animated")) {
                $(this).animate({ width: "+=50px", height: "+=50px" }, { duration: 2000 });
            }
        });
    </script>
	<!-- Stopka -->
    <footer>
        <?php
        $nr_indeksu = 169244;
        $nrGrupy = '3';
        $autor = 'Marcin Gwiazda';
        echo "Autor: $autor, Nr Indeksu: $nr_indeksu, Grupa: $nrGrupy";
        ?>
    </footer>
</body>
</html>
