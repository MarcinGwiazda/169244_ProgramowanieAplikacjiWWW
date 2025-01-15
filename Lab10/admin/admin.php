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
          </nav><hr>';
}

// Obsługa logowania
if (isset($_POST['zaloguj'])) {
    $email = $_POST['login_email'];
    $password = $_POST['login_pass'];

    if ($email === $login && $password === $pass) {
        $_SESSION['zalogowany'] = true;
    } else {
        echo '<p>Nieprawidłowy login lub hasło!</p>';
    }
}

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

    // Formularz dodawania nowej podstrony
    echo '<form method="post">
        <label for="page_title">Tytuł strony:</label><br>
        <input type="text" id="page_title" name="page_title" required /><br><br>

        <label for="page_content">Treść strony:</label><br>
        <textarea id="page_content" name="page_content" rows="10" cols="50" required></textarea><br><br>

        <label for="status">Aktywna:</label>
        <input type="checkbox" id="status" name="status" /><br><br>

        <input type="submit" value="Dodaj podstronę" />
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
        default:
            echo '<p>Nieznana akcja!</p>';
    }
} else {
    echo '<p>Wybierz jedną z opcji powyżej, aby zarządzać podstronami lub kategoriami.</p>';
}

?>
</body>
</html>
