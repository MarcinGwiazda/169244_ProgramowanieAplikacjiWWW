<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administracyjny</title>
    <link rel="stylesheet" href="../css/admin.css">
</head>
<?php
// Rozpoczęcie sesji
session_start();
include('../cfg.php');

// Połączenie z bazą danych
$link = mysqli_connect('localhost', 'root', '', 'moja_strona');
if (!$link) {
    die('Błąd połączenia MySQLi: ' . mysqli_connect_error());
}

// Funkcja wyświetlająca listę podstron
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
        echo '<td>' . $row['id'] . '</td>';
        echo '<td>' . htmlspecialchars($row['page_title']) . '</td>';
        echo '<td>' . ($row['status'] ? 'Aktywna' : 'Nieaktywna') . '</td>';
        echo '<td>';
        echo '<a href="admin.php?edit=' . $row['id'] . '">Edytuj</a> | ';
        echo '<a href="admin.php?delete=' . $row['id'] . '">Usuń</a>';
        echo '</td>';
        echo '</tr>';
    }

    echo '</table>';
    echo '<br><a href="admin.php?add">Dodaj nową podstronę</a>';
}

// Funkcja dodawania nowej podstrony
function DodajNowaPodstrone() {
    echo '<h2>Dodaj nową podstronę</h2>';
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

// Obsługa dodawania podstrony
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_new_page'])) {
    $page_title = $_POST['page_title'];
    $page_content = $_POST['page_content'];
    $status = isset($_POST['status']) ? 1 : 0;

    $query = "INSERT INTO page_list (page_title, page_content, status) VALUES (?, ?, ?)";
    $stmt = $link->prepare($query);
    $stmt->bind_param("ssi", $page_title, $page_content, $status);
    $stmt->execute();
    $stmt->close();

    header('Location: admin.php');
    exit;
}

// Obsługa wyświetlenia formularza dodawania podstrony
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['show_add_page_form'])) {
    DodajNowaPodstrone();
    exit;
}

// Obsługa edycji podstrony
if (isset($_GET['edit'])) {
    $id = intval($_GET['edit']);
    $query = "SELECT * FROM page_list WHERE id = ?";
    $stmt = $link->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        echo '<h2>Edytuj podstronę</h2>';
        echo '<form method="post">
            <label for="page_title">Tytuł strony:</label><br>
            <input type="text" id="page_title" name="page_title" value="' . htmlspecialchars($row['page_title']) . '" required /><br><br>

            <label for="page_content">Treść strony:</label><br>
            <textarea id="page_content" name="page_content" rows="10" cols="50" required>' . htmlspecialchars($row['page_content']) . '</textarea><br><br>

            <label for="status">Aktywna:</label>
            <input type="checkbox" id="status" name="status" ' . ($row['status'] ? 'checked' : '') . ' /><br><br>

            <input type="hidden" name="id" value="' . $id . '">
            <input type="submit" name="save_changes" value="Zapisz zmiany" />
        </form>';
    }
    exit;
}

// Obsługa usuwania podstrony
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $query = "DELETE FROM page_list WHERE id = ?";
    $stmt = $link->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header('Location: admin.php');
    exit;
}

// Obsługa zapisu zmian w podstronie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_changes'])) {
    $id = $_POST['id'];
    $page_title = $_POST['page_title'];
    $page_content = $_POST['page_content'];
    $status = isset($_POST['status']) ? 1 : 0;

    $query = "UPDATE page_list SET page_title = ?, page_content = ?, status = ? WHERE id = ?";
    $stmt = $link->prepare($query);
    $stmt->bind_param("ssii", $page_title, $page_content, $status, $id);
    $stmt->execute();
    $stmt->close();

    header('Location: admin.php');
    exit;
}

// Przycisk powrotu do strony głównej
echo '<a href="../index.php" class="button">Powrót do strony głównej</a>';

echo '<h2>Lista podstron</h2>';
ListaPodstron();

// Funkcja wyświetlająca drzewo kategorii
function PokazKategorie($parent_id = 0, $level = 0) {
    global $link;
    $stmt = $link->prepare("SELECT * FROM kategorie WHERE matka = ? ORDER BY id ASC");
    $stmt->bind_param("i", $parent_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo str_repeat('&nbsp;&nbsp;&nbsp;', $level) . htmlspecialchars($row['nazwa']);
        echo " <a href='?edit_id=" . $row['id'] . "'>[Edytuj]</a>";
        echo " <a href='?delete_id=" . $row['id'] . "'>[Usuń]</a><br>";

        PokazKategorie($row['id'], $level + 1);
    }

    $stmt->close();
}

// Dodawanie kategorii
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dodaj_kategorie'])) {
    $nazwa = $_POST['nazwa'];
    $matka = $_POST['matka'];
    $stmt = $link->prepare("INSERT INTO kategorie (nazwa, matka) VALUES (?, ?)");
    $stmt->bind_param("si", $nazwa, $matka);
    $stmt->execute();
    $stmt->close();
}

// Usuwanie kategorii
if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $stmt = $link->prepare("DELETE FROM kategorie WHERE matka = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    $stmt = $link->prepare("DELETE FROM kategorie WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header('Location: admin.php');
    exit;
}

// Edycja kategorii
if (isset($_GET['edit_id'])) {
    $id = intval($_GET['edit_id']);
    $stmt = $link->prepare("SELECT * FROM kategorie WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $kategoria = $result->fetch_assoc();
    $stmt->close();

    echo '<h2>Edytuj kategorię</h2>';
    echo '<form method="post">
        <input type="hidden" name="id" value="' . $kategoria['id'] . '">
        <label for="nazwa">Nazwa:</label>
        <input type="text" name="nazwa" value="' . htmlspecialchars($kategoria['nazwa']) . '" required>
        <label for="matka">Kategoria nadrzędna:</label>
        <select name="matka">
            <option value="0">Brak (główna kategoria)</option>';
    $result = $link->query("SELECT * FROM kategorie WHERE id != $id");
    while ($row = $result->fetch_assoc()) {
        $selected = $row['id'] == $kategoria['matka'] ? 'selected' : '';
        echo '<option value="' . $row['id'] . '" ' . $selected . '>' . htmlspecialchars($row['nazwa']) . '</option>';
    }
    echo '</select>
        <button type="submit" name="edytuj_kategorie">Zapisz</button>
    </form>';
    exit;
}

// Zapisanie edycji kategorii
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edytuj_kategorie'])) {
    $id = $_POST['id'];
    $nazwa = $_POST['nazwa'];
    $matka = $_POST['matka'];

    $stmt = $link->prepare("UPDATE kategorie SET nazwa = ?, matka = ? WHERE id = ?");
    $stmt->bind_param("sii", $nazwa, $matka, $id);
    $stmt->execute();
    $stmt->close();

    header('Location: admin.php');
    exit;
}

// Zarządzanie kategoriami
echo '<h2>Zarządzanie kategoriami</h2>';
echo '<form method="post">
    <label for="nazwa">Nazwa kategorii:</label>
    <input type="text" name="nazwa" required>
    <label for="matka">Kategoria nadrzędna:</label>
    <select name="matka">
        <option value="0">Brak (główna kategoria)</option>';
$result = $link->query("SELECT * FROM kategorie");
while ($row = $result->fetch_assoc()) {
    echo '<option value="' . $row['id'] . '">' . htmlspecialchars($row['nazwa']) . '</option>';
}
echo '</select>
    <button type="submit" name="dodaj_kategorie">Dodaj kategorię</button>
</form>';

PokazKategorie();
?>
