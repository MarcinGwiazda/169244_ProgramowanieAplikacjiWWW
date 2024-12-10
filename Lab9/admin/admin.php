<?php
// Rozpoczęcie sesji
session_start();

// Dołączenie plików konfiguracyjnych i funkcji
include('../cfg.php'); 
include('../showpage.php'); 

/**
  Formularz logowania
  Tworzy formularz logowania do panelu admina.
 */
function FormularzLogowania() {
    return '
    <div class="logowanie">
        <h1 class="heading">Panel CMS</h1>
        <form method="post" name="loginForm" action="'.htmlspecialchars($_SERVER['REQUEST_URI']).'">
            <table class="logowanie">
                <tr><td class="log_t">Email</td><td><input type="text" name="login_email" class="logowanie" required /></td></tr>
                <tr><td class="log_t">Hasło</td><td><input type="password" name="login_pass" class="logowanie" required /></td></tr>
                <tr><td>&nbsp;</td><td><input type="submit" name="zaloguj" class="logowanie" value="Zaloguj" /></td></tr>
            </table>
        </form>
    </div>';
}

/**
  Lista podstron
  Pobiera i wyświetla listę podstron w formacie tabeli.
 */
function ListaPodstron() {
    global $link;

    // Pobranie podstron z bazy danych
    $query = "SELECT * FROM page_list ORDER BY id ASC";
    $result = mysqli_query($link, $query);

    // Wyświetlenie tabeli z podstronami
    echo '<table border="1" cellpadding="10" cellspacing="0">';
    echo '<tr><th>ID</th><th>Tytuł podstrony</th><th>Status</th><th>Akcje</th></tr>';

    while ($row = mysqli_fetch_assoc($result)) {
        echo '<tr>';
        echo '<td>'.$row['id'].'</td>';
        echo '<td>'.htmlspecialchars($row['page_title']).'</td>';
        echo '<td>'.($row['status'] ? 'Aktywna' : 'Nieaktywna').'</td>';
        echo '<td>';
        echo '<a href="admin.php?edit='.$row['id'].'">Edytuj</a> | ';
        echo '<a href="admin.php?delete='.$row['id'].'">Usuń</a>';
        echo '</td>';
        echo '</tr>';
    }

    echo '</table>';
    echo '<br><a href="admin.php?add">Dodaj nową podstronę</a>';
}

/**
  Edycja podstrony
  Pozwala na edytowanie istniejącej podstrony.
 */
function EdytujPodstrone($id) {
    global $link;

    // Pobranie danych podstrony
    $query = "SELECT * FROM page_list WHERE id = $id LIMIT 1";
    $result = mysqli_query($link, $query);

    if ($row = mysqli_fetch_assoc($result)) {
        // Obsługa zapisania zmian
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $page_title = $_POST['page_title'];
            $page_content = $_POST['page_content'];
            $status = isset($_POST['status']) ? 1 : 0;

            // Aktualizacja danych w bazie
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

        // Formularz edycji
        echo '<form method="post">
            <label for="page_title">Tytuł strony:</label><br>
            <input type="text" id="page_title" name="page_title" value="'.htmlspecialchars($row['page_title']).'" required /><br><br>

            <label for="page_content">Treść strony:</label><br>
            <textarea id="page_content" name="page_content" rows="10" cols="50" required>'.htmlspecialchars($row['page_content']).'</textarea><br><br>

            <label for="status">Aktywna:</label>
            <input type="checkbox" id="status" name="status" '.($row['status'] ? 'checked' : '').' /><br><br>

            <input type="submit" value="Zapisz zmiany" />
        </form>';
    } else {
        echo '<p>Nie znaleziono podstrony o podanym ID.</p>';
    }
}

/**
  Dodanie nowej podstrony
  Pozwala na dodanie nowej podstrony do systemu.
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
            header("Location: admin.php"); // Przekierowanie po dodaniu
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
  Usunięcie podstrony
  Usuwa podstronę z systemu.
 */
function UsunPodstrone($id) {
    global $link;

    $delete_query = "DELETE FROM page_list WHERE id = $id LIMIT 1";

    if (mysqli_query($link, $delete_query)) {
        header("Location: admin.php"); // Przekierowanie po usunięciu
        exit;
    } else {
        echo '<p>Błąd podczas usuwania: ' . mysqli_error($link) . '</p>';
    }
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

// Weryfikacja sesji logowania
if (!isset($_SESSION['zalogowany']) || $_SESSION['zalogowany'] !== true) {
    echo FormularzLogowania();
    exit;
}

// Obsługa akcji (edycja, usunięcie, dodanie podstrony)
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    EdytujPodstrone((int)$_GET['edit']);
} elseif (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    UsunPodstrone((int)$_GET['delete']);
} elseif (isset($_GET['add'])) {
    DodajNowaPodstrone();
} else {
    ListaPodstron();
}
?>
