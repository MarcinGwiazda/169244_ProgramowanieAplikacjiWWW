<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administracyjny</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<body>

<?php
session_start();

include('../cfg.php');
include('../showpage.php');

$link = mysqli_connect('localhost', 'root', '', 'moja_strona');
if (!$link) {
    die('Błąd połączenia MySQLi: ' . mysqli_connect_error());
}

// Przycisk powrotu do strony głównej
echo '<br><br><a href="../index.php" class="button">Powrót do strony głównej</a>';

/**
 * Formularz logowania
 */
function FormularzLogowania() {
    return '
    <div class="logowanie">
        <h1 class="heading">Panel CMS</h1>
        <form method="post" name="loginForm" action="'.htmlspecialchars($_SERVER['REQUEST_URI']).'">
            <table class="logowanie">
                <tr><td>Email</td><td><input type="text" name="login_email" required /></td></tr>
                <tr><td>Hasło</td><td><input type="password" name="login_pass" required /></td></tr>
                <tr><td></td><td><input type="submit" name="zaloguj" value="Zaloguj" /></td></tr>
            </table>
        </form>
    </div>';
}

/**
 * Nawigacja
 */
function Nawigacja() {
    echo '<nav>
            <a href="admin.php?action=pages" class="button">Zarządzaj Podstronami</a>
            <a href="admin.php?action=categories" class="button">Zarządzaj Kategoriami</a>
            <a href="admin.php?action=produkty" class="button">Zarządzaj Produktami</a>
       
            <a href="admin.php?logout=true" class="button logout">Wyloguj</a>
          </nav><hr>';
}

/**
 * Obsługa logowania
 */
if (isset($_POST['zaloguj'])) {
    $email = $_POST['login_email'];
    $password = $_POST['login_pass'];

    if ($email === $login && $password === $pass) {
        $_SESSION['zalogowany'] = true;
    } else {
        echo '<p style="color: red;">Nieprawidłowy login lub hasło!</p>';
    }
}

/**
 * Obsługa wylogowania
 */
if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    session_unset();  // Usunięcie wszystkich danych sesji
    session_destroy();  // Zniszczenie sesji
    header("Location: admin.php");  // Przekierowanie do strony logowania
    exit;
}

// Sprawdzenie, czy użytkownik jest zalogowany
if (!isset($_SESSION['zalogowany'])) {
    echo FormularzLogowania();
    exit;
}

// Wyświetlenie nawigacji
Nawigacja();

/**
 * Lista podstron
 */
function ListaPodstron() {
    global $link;

    $query = "SELECT * FROM page_list ORDER BY id ASC";
    $result = mysqli_query($link, $query);

    echo '<h2>Lista podstron</h2>';
    echo '<table border="1" cellpadding="10" cellspacing="0">';
    echo '<tr><th>ID</th><th>Tytuł podstrony</th><th>Status</th><th>Akcje</th></tr>';

    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>'.$row['id'].'</td>';
        echo '<td>'.htmlspecialchars($row['page_title']).'</td>';
        echo '<td>'.($row['status'] ? 'Aktywna' : 'Nieaktywna').'</td>';
        echo '<td>
                <a href="admin.php?action=edit_page&id='.$row['id'].'">Edytuj</a> | 
                <a href="admin.php?action=delete_page&id='.$row['id'].'">Usuń</a>
              </td>';
        echo '</tr>';
    }

    echo '</table>';
    echo '<br><a href="admin.php?action=add_page" class="button">Dodaj nową podstronę</a>';
}

/**
 * Dodanie nowej podstrony
 */
function DodajNowaPodstrone() {
    global $link;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $page_title = $_POST['page_title'];
        $page_content = $_POST['page_content'];
        $status = isset($_POST['status']) ? 1 : 0;

        // Wstawienie nowej podstrony do bazy
        $insert_query = "INSERT INTO page_list (page_title, page_content, status) VALUES (
                            '".mysqli_real_escape_string($link, $page_title)."',
                            '".mysqli_real_escape_string($link, $page_content)."',
                            $status)";

        if (mysqli_query($link, $insert_query)) {
            header("Location: admin.php?action=pages"); // Przekierowanie po dodaniu
            exit;
        } else {
            echo '<p>Błąd podczas dodawania: ' . mysqli_error($link) . '</p>';
        }
    }

    // Formularz dodawania nowej podstrony z przyciskiem "Anuluj"
    echo '<h2>Dodaj nową podstronę</h2>
    <form method="post">
        <label for="page_title">Tytuł strony:</label><br>
        <input type="text" id="page_title" name="page_title" required /><br><br>

        <label for="page_content">Treść strony:</label><br>
        <textarea id="page_content" name="page_content" rows="10" cols="50" required></textarea><br><br>

        <label for="status">Aktywna:</label>
        <input type="checkbox" id="status" name="status" /><br><br>

        <input type="submit" value="Dodaj podstronę" />
        <a href="admin.php?action=pages" class="button" style="padding: 5px 10px; background-color: #ccc; text-decoration: none; margin-left: 10px;">Anuluj</a>
    </form>';
}

/**
 * Edycja podstrony
 */
function EdytujPodstrone($id) {
    global $link;

    $query = "SELECT * FROM page_list WHERE id = $id LIMIT 1";
    $result = mysqli_query($link, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $page_title = $_POST['page_title'];
            $page_content = $_POST['page_content'];
            $status = isset($_POST['status']) ? 1 : 0;

            $update_query = "UPDATE page_list SET 
                                page_title = '".mysqli_real_escape_string($link, $page_title)."',
                                page_content = '".mysqli_real_escape_string($link, $page_content)."',
                                status = $status
                             WHERE id = $id LIMIT 1";

            if (mysqli_query($link, $update_query)) {
                echo '<p>Dane zostały pomyślnie zaktualizowane.</p>';
            } else {
                echo '<p>Błąd podczas aktualizacji: ' . mysqli_error($link) . '</p>';
            }
        }

        echo '<h2>Edytuj podstronę</h2>
        <form method="post">
            <label for="page_title">Tytuł strony:</label><br>
            <input type="text" id="page_title" name="page_title" value="'.htmlspecialchars($row['page_title']).'" required /><br><br>

            <label for="page_content">Treść strony:</label><br>
            <textarea id="page_content" name="page_content" rows="10" cols="50" required>'.htmlspecialchars($row['page_content']).'</textarea><br><br>

            <label for="status">Aktywna:</label>
            <input type="checkbox" id="status" name="status" '.($row['status'] ? 'checked' : '').' /><br><br>

            <input type="submit" value="Zapisz zmiany" />
        </form>';

        //  Dodany przycisk powrotu do strony głównej
        echo '<br><a href="../admin/admin.php?action=pages" class="button">Powrót</a>';
    } else {
        echo '<p>Nie znaleziono podstrony o podanym ID.</p>';
    }
}

/**
 * Usuwanie podstrony
 */
function UsunPodstrone($id) {
    global $link;

    // Przygotowanie zapytania do usunięcia podstrony
    $stmt = $link->prepare("DELETE FROM page_list WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo '<p style="color: green;">Podstrona została pomyślnie usunięta.</p>';
    } else {
        echo '<p style="color: red;">Błąd podczas usuwania podstrony: ' . $stmt->error . '</p>';
    }

    $stmt->close();

    // Przekierowanie po usunięciu
    header("Location: admin.php?action=pages");
    exit;
}

/**
 * Obsługa żądania usunięcia podstrony
 */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    UsunPodstrone($id);
}

/**
 * Lista kategorii
 */
function ListaKategorii() {
    global $link;

    $query = "SELECT * FROM kategorie ORDER BY id ASC";
    $result = mysqli_query($link, $query);

    echo '<h2>Lista kategorii</h2>';
    echo '<ul>';

    while ($row = mysqli_fetch_assoc($result)) {
        echo '<li>' . htmlspecialchars($row['nazwa']);
        echo " <a href='admin.php?action=edit_category&id=" . $row['id'] . "'>[Edytuj]</a>";
        echo " <a href='admin.php?action=delete_category&id=" . $row['id'] . "'>[Usuń]</a>";
        echo '</li>';
    }

    echo '</ul>';
    echo '<br><a href="admin.php?action=add_category" class="button">Dodaj nową kategorię</a>';
}

/**
 * Dodanie nowej kategorii
 */
function DodajKategorie() {
    echo '<h2>Dodaj nową kategorię</h2>
    <form method="post" action="">
        <label for="nazwa">Nazwa kategorii:</label><br>
        <input type="text" name="nazwa" required><br><br>

        <label for="matka">Kategoria nadrzędna:</label><br>
        <select name="matka">
            <option value="0">Brak (główna kategoria)</option>';

    global $link;
    $result = $link->query("SELECT * FROM kategorie");
    while ($row = $result->fetch_assoc()) {
        echo '<option value="'.$row['id'].'">'.htmlspecialchars($row['nazwa']).'</option>';
    }

    echo '</select><br><br>
        <button type="submit" name="dodaj_kategorie">Dodaj kategorię</button>
		<a href="admin.php?action=categories" class="button" style="padding: 5px 10px; background-color: #ccc; text-decoration: none; margin-left: 10px;">Anuluj</a>
    </form>';
}

/**
 * Obsługa dodawania kategorii do bazy
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dodaj_kategorie'])) {
    global $link;

    $nazwa = htmlspecialchars($_POST['nazwa']);
    $matka = isset($_POST['matka']) ? (int)$_POST['matka'] : 0;

    if (!empty($nazwa)) {
        $stmt = $link->prepare("INSERT INTO kategorie (nazwa, matka) VALUES (?, ?)");
        $stmt->bind_param("si", $nazwa, $matka);

        if ($stmt->execute()) {
            echo '<p style="color: green;">Dodano nową kategorię!</p>';
        } else {
            echo '<p style="color: red;">Błąd podczas dodawania kategorii: ' . $stmt->error . '</p>';
        }
        $stmt->close();

        // Odświeżenie strony po dodaniu kategorii
        header('Location: admin.php?action=categories');
        exit;
    } else {
        echo '<p style="color: red;">Podaj nazwę kategorii!</p>';
    }
}


/**
 * Edycja kategorii
 */
function EdytujKategorie($id) {
    global $link;

    $stmt = $link->prepare("SELECT * FROM kategorie WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $kategoria = $result->fetch_assoc();

    echo '<h2>Edytuj kategorię</h2>';
    echo '<form method="post">
        <input type="hidden" name="id" value="' . $kategoria['id'] . '">
        <label for="nazwa">Nazwa:</label><br>
        <input type="text" name="nazwa" value="' . htmlspecialchars($kategoria['nazwa']) . '" required><br><br>

        <button type="submit" name="edytuj_kategorie">Zapisz zmiany</button>
        <a href="admin.php?action=categories" class="button">Powrót</a>
    </form>';
}

function UsunKategorie($id) {
    global $link;

    // Najpierw usuwamy wszystkie podkategorie
    $stmt = $link->prepare("DELETE FROM kategorie WHERE matka = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo '<p style="color: green;">Podkategorie zostały usunięte.</p>';
    } else {
        echo '<p style="color: red;">Błąd podczas usuwania podkategorii: ' . $stmt->error . '</p>';
    }
    $stmt->close();

    // Następnie usuwamy wybraną kategorię
    $stmt = $link->prepare("DELETE FROM kategorie WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo '<p style="color: green;">Kategoria została usunięta.</p>';
    } else {
        echo '<p style="color: red;">Błąd podczas usuwania kategorii: ' . $stmt->error . '</p>';
    }
    $stmt->close();

    // Przekierowanie po usunięciu
    header('Location: admin.php?action=categories');
    exit;
}

/**
 * Obsługa żądania usunięcia kategorii
 */
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    UsunKategorie($id);
}


function PokazProdukty() {
    global $link;

    $query = "SELECT p.*, c.nazwa AS kategoria_nazwa 
              FROM produkty p 
              LEFT JOIN kategorie c ON p.kategoria = c.id";
    $result = mysqli_query($link, $query);

    echo "<h2>Lista produktów</h2>";
    echo "<a href='?action=add_product' class='button'>Dodaj nowy produkt</a><br><br>";
    echo "<table border='1'>
        <tr>
            <th>ID</th>
            <th>Tytuł</th>
            <th>Opis</th>
            <th>Cena netto</th>
            <th>VAT</th>
            <th>Ilość w magazynie</th>
            <th>Status</th>
            <th>Kategoria</th>
            <th>Gabaryt</th>
            <th>Zdjęcie</th>
            <th>Data utworzenia</th>
            <th>Data modyfikacji</th>
            <th>Data wygaśnięcia</th>
            <th>Akcje</th>
        </tr>";

    while ($row = mysqli_fetch_assoc($result)) {
        $status = $row['status_dostepnosci'] ? 'Dostępny' : 'Niedostępny';
        echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['tytul']}</td>
            <td>{$row['opis']}</td>
            <td>{$row['cena_netto']} zł</td>
            <td>{$row['podatek_vat']}%</td>
            <td>{$row['ilosc_magazyn']}</td>
            <td>{$status}</td>
            <td>{$row['kategoria_nazwa']}</td>
            <td>{$row['gabaryt']}</td>
            <td><img src='../{$row['zdjecie']}' alt='Zdjęcie produktu' style='max-width:100px;'></td>
            <td>{$row['data_utworzenia']}</td>
            <td>{$row['data_modyfikacji']}</td>
            <td>{$row['data_wygasniecia']}</td>
            <td>
                <a href='?action=edit_product&id={$row['id']}'>Edytuj</a> | 
                <a href='?action=delete_product&id={$row['id']}'>Usuń</a>
            </td>
        </tr>";
    }
    echo "</table>";
}


function DodajProdukt() {
    global $link;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['dodaj'])) {
            $tytul = $_POST['tytul'];
            $opis = $_POST['opis'];
            $cena_netto = $_POST['cena_netto'];
            $podatek_vat = $_POST['podatek_vat'];
            $ilosc_magazyn = $_POST['ilosc_magazyn'];
            $status_dostepnosci = isset($_POST['status_dostepnosci']) ? 1 : 0;
            $kategoria = $_POST['kategoria'];
            $gabaryt = $_POST['gabaryt'];
            $zdjecie = $_POST['zdjecie'];
            $data_wygasniecia = $_POST['data_wygasniecia'];

            $stmt = $link->prepare("INSERT INTO produkty (tytul, opis, cena_netto, podatek_vat, ilosc_magazyn, status_dostepnosci, kategoria, gabaryt, zdjecie, data_wygasniecia)
                                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdiisisss", $tytul, $opis, $cena_netto, $podatek_vat, $ilosc_magazyn, $status_dostepnosci, $kategoria, $gabaryt, $zdjecie, $data_wygasniecia);
            $stmt->execute();

            header("Location: admin.php?action=produkty");
            exit;
        }
    }

    // Formularz z działającym przyciskiem "Anuluj"
    echo "<h2>Dodaj produkt</h2>
    <form method='POST'>
        <label>Tytuł: <input type='text' name='tytul' required></label><br><br>
        <label>Opis: <textarea name='opis' required></textarea></label><br><br>
        <label>Cena netto: <input type='number' step='0.01' name='cena_netto' required></label><br><br>
        <label>Podatek VAT: <input type='number' name='podatek_vat' required></label><br><br>
        <label>Ilość: <input type='number' name='ilosc_magazyn' required></label><br><br>
        <label>Status dostępności: <input type='checkbox' name='status_dostepnosci'></label><br><br>
        
        <label>Kategoria: <select name='kategoria' required>";
    
    $result = $link->query("SELECT id, nazwa FROM kategorie");
    while ($row = $result->fetch_assoc()) {
        echo "<option value='{$row['id']}'>{$row['nazwa']}</option>";
    }

    echo "</select></label><br><br>
        <label>Gabaryt: <input type='text' name='gabaryt'></label><br><br>
        <label>Zdjęcie (link): <input type='text' name='zdjecie'></label><br><br>
        <label>Data wygaśnięcia: <input type='date' name='data_wygasniecia'></label><br><br>

        <input type='submit' name='dodaj' value='Dodaj'>
        <a href='admin.php?action=produkty' class='button' style='padding: 5px 10px; background-color: #ccc; text-decoration: none;'>Anuluj</a>
    </form>";
}

function EdytujProdukt($id) {
    global $link;

    // Pobranie danych produktu
    $stmt = $link->prepare("SELECT * FROM produkty WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $produkt = $result->fetch_assoc();
    $stmt->close();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $tytul = $_POST['tytul'];
        $opis = $_POST['opis'];
        $cena_netto = $_POST['cena_netto'];
        $podatek_vat = $_POST['podatek_vat'];
        $ilosc_magazyn = $_POST['ilosc_magazyn'];
        $status_dostepnosci = isset($_POST['status_dostepnosci']) ? 1 : 0;
        $kategoria = $_POST['kategoria'];
        $gabaryt = $_POST['gabaryt'];
        $zdjecie = $_POST['zdjecie'];
        $data_wygasniecia = $_POST['data_wygasniecia'];

        // Aktualizacja wszystkich danych (bez zmiany daty utworzenia)
        $stmt = $link->prepare("UPDATE produkty SET 
            tytul = ?, 
            opis = ?, 
            cena_netto = ?, 
            podatek_vat = ?, 
            ilosc_magazyn = ?, 
            status_dostepnosci = ?, 
            kategoria = ?, 
            gabaryt = ?, 
            zdjecie = ?, 
            data_wygasniecia = ? 
            WHERE id = ?");
        
        $stmt->bind_param("ssdiisisssi", 
            $tytul, 
            $opis, 
            $cena_netto, 
            $podatek_vat, 
            $ilosc_magazyn, 
            $status_dostepnosci, 
            $kategoria, 
            $gabaryt, 
            $zdjecie, 
            $data_wygasniecia, 
            $id
        );

        $stmt->execute();
        $stmt->close();

        header("Location: admin.php?action=produkty");
        exit;
    }

    // Formularz edycji produktu
    echo "<h2>Edytuj Produkt</h2>
    <form method='POST'>
        <label for='tytul'>Tytuł:</label><br>
        <input type='text' name='tytul' value='{$produkt['tytul']}' required><br><br>

        <label for='opis'>Opis:</label><br>
        <textarea name='opis' rows='5' cols='50' required>{$produkt['opis']}</textarea><br><br>

        <label for='cena_netto'>Cena netto:</label><br>
        <input type='number' step='0.01' name='cena_netto' value='{$produkt['cena_netto']}' required><br><br>

        <label for='podatek_vat'>Podatek VAT (%):</label><br>
        <input type='number' name='podatek_vat' value='{$produkt['podatek_vat']}' required><br><br>

        <label for='ilosc_magazyn'>Ilość w magazynie:</label><br>
        <input type='number' name='ilosc_magazyn' value='{$produkt['ilosc_magazyn']}' required><br><br>

        <label for='status_dostepnosci'>Status dostępności:</label><br>
        <input type='checkbox' name='status_dostepnosci' ". ($produkt['status_dostepnosci'] ? 'checked' : '') ."><br><br>

        <label for='kategoria'>Kategoria:</label><br>
        <input type='number' name='kategoria' value='{$produkt['kategoria']}' required><br><br>

        <label for='gabaryt'>Gabaryt:</label><br>
        <input type='text' name='gabaryt' value='{$produkt['gabaryt']}'><br><br>

        <label for='zdjecie'>Link do zdjęcia:</label><br>
        <input type='text' name='zdjecie' value='{$produkt['zdjecie']}'><br><br>

        <label for='data_wygasniecia'>Data wygaśnięcia:</label><br>
        <input type='date' name='data_wygasniecia' value='{$produkt['data_wygasniecia']}'><br><br>

        <input type='submit' value='Zapisz zmiany'>
        <a href='admin.php?action=produkty'><button type='button'>Anuluj</button></a>
    </form>";
}

function UsunProdukt($id) {
    global $link;

    $stmt = $link->prepare("DELETE FROM produkty WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: admin.php?action=produkty");
    exit;
}


/**
 * Obsługa akcji
 */
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'pages':
            ListaPodstron();
            break;
        case 'categories':
            ListaKategorii();
            break;
        case 'edit_page':
            EdytujPodstrone((int)$_GET['id']);
            break;
        case 'edit_category':
            EdytujKategorie((int)$_GET['id']);
            break;
		case 'add_page':
            DodajNowaPodstrone();
            break;
        case 'add_category':
            DodajKategorie();
            break;
        case 'delete_page':
            UsunPodstrone((int)$_GET['id']);
            break;
        case 'delete_category':
            UsunKategorie((int)$_GET['id']);
            break;
		case 'produkty':
            PokazProdukty();
            break;
        case 'add_product':
            DodajProdukt();
            break;
        case 'edit_product':
            EdytujProdukt((int)$_GET['id']);
            break;
        case 'delete_product':
            UsunProdukt((int)$_GET['id']);
            break;
        default:
            echo '<p>Nieznana akcja!</p>';
    }
} else {
    echo '<p>Wybierz jedną z opcji powyżej, aby zarządzać podstronami lub kategoriami.</p>';
}

?>
</body>
</html>
