<?php
// Połączenie z bazą danych
$link = mysqli_connect('localhost', 'root', '', 'moja_strona');
if (!$link) {
    die('Błąd połączenia MySQLi: ' . mysqli_connect_error());
}

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

    header('Location: ' . $_SERVER['PHP_SELF']);
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

    header('Location: ' . $_SERVER['PHP_SELF']);
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