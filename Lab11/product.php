<?php
// Połączenie z bazą danych
$link = mysqli_connect('localhost', 'root', '', 'moja_strona');
if (!$link) {
    die('Błąd połączenia MySQLi: ' . mysqli_connect_error());
}

// Funkcja: DodajProdukt
function DodajProdukt() {
    echo '<h2>Dodaj nowy produkt</h2>';
    echo '<form method="post" enctype="multipart/form-data">
        <label for="tytul">Tytuł:</label>
        <input type="text" name="tytul" required><br><br>
        
        <label for="opis">Opis:</label>
        <textarea name="opis" rows="5" cols="50" required></textarea><br><br>
        
        <label for="data_wygasniecia">Data wygaśnięcia:</label>
        <input type="date" name="data_wygasniecia"><br><br>
        
        <label for="cena_netto">Cena netto:</label>
        <input type="number" step="0.01" name="cena_netto" required><br><br>
        
        <label for="vat">Podatek VAT (%):</label>
        <input type="number" step="0.01" name="vat" required><br><br>
        
        <label for="ilosc">Ilość dostępnych sztuk:</label>
        <input type="number" name="ilosc" required><br><br>
        
        <label for="status">Status dostępności:</label>
        <select name="status">
            <option value="1">Dostępny</option>
            <option value="0">Niedostępny</option>
        </select><br><br>
        
        <label for="kategoria">Kategoria:</label>
        <input type="text" name="kategoria"><br><br>
        
        <label for="gabaryt">Gabaryt:</label>
        <input type="text" name="gabaryt"><br><br>
        
        <label for="zdjecie">Zdjęcie (link):</label>
        <input type="text" name="zdjecie"><br><br>
        
        <button type="submit" name="dodaj_produkt">Dodaj produkt</button>
    </form>';
}

// Obsługa dodawania produktu
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dodaj_produkt'])) {
    $tytul = $_POST['tytul'];
    $opis = $_POST['opis'];
    $data_utworzenia = date('Y-m-d');
    $data_modyfikacji = $data_utworzenia;
    $data_wygasniecia = $_POST['data_wygasniecia'];
    $cena_netto = $_POST['cena_netto'];
    $vat = $_POST['vat'];
    $ilosc = $_POST['ilosc'];
    $status = $_POST['status'];
    $kategoria = $_POST['kategoria'];
    $gabaryt = $_POST['gabaryt'];
    $zdjecie = $_POST['zdjecie'];

    $query = "INSERT INTO produkty 
              (tytul, opis, data_utworzenia, data_modyfikacji, data_wygasniecia, cena_netto, vat, ilosc, status, kategoria, gabaryt, zdjecie) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $link->prepare($query);
    $stmt->bind_param("sssssdidisds", $tytul, $opis, $data_utworzenia, $data_modyfikacji, $data_wygasniecia, $cena_netto, $vat, $ilosc, $status, $kategoria, $gabaryt, $zdjecie);
    $stmt->execute();
    $stmt->close();

    echo '<p>Produkt został dodany.</p>';
}

// Funkcja: PokazProdukty
function PokazProdukty() {
    global $link;
    $result = $link->query("SELECT * FROM produkty ORDER BY id ASC");

    echo '<h2>Lista produktów</h2>';
    echo '<table border="1" cellpadding="10" cellspacing="0">';
    echo '<tr><th>ID</th><th>Tytuł</th><th>Cena netto</th><th>VAT</th><th>Ilość</th><th>Status</th><th>Akcje</th></tr>';
    while ($row = $result->fetch_assoc()) {
        $status = $row['status'] ? 'Dostępny' : 'Niedostępny';
        echo '<tr>';
        echo '<td>' . $row['id'] . '</td>';
        echo '<td>' . htmlspecialchars($row['tytul']) . '</td>';
        echo '<td>' . number_format($row['cena_netto'], 2) . '</td>';
        echo '<td>' . $row['vat'] . '%</td>';
        echo '<td>' . $row['ilosc'] . '</td>';
        echo '<td>' . $status . '</td>';
        echo '<td>
            <a href="?edit_produkt=' . $row['id'] . '">Edytuj</a> | 
            <a href="?delete_produkt=' . $row['id'] . '">Usuń</a>
        </td>';
        echo '</tr>';
    }
    echo '</table>';
}

// Funkcja: EdytujProdukt
function EdytujProdukt($id) {
    global $link;
    $query = "SELECT * FROM produkty WHERE id = ?";
    $stmt = $link->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $produkt = $result->fetch_assoc();
    $stmt->close();

    echo '<h2>Edytuj produkt</h2>';
    echo '<form method="post">
        <input type="hidden" name="id" value="' . $produkt['id'] . '">
        <label for="tytul">Tytuł:</label>
        <input type="text" name="tytul" value="' . htmlspecialchars($produkt['tytul']) . '" required><br><br>
        
        <label for="opis">Opis:</label>
        <textarea name="opis" rows="5" cols="50" required>' . htmlspecialchars($produkt['opis']) . '</textarea><br><br>
        
        <label for="data_wygasniecia">Data wygaśnięcia:</label>
        <input type="date" name="data_wygasniecia" value="' . $produkt['data_wygasniecia'] . '"><br><br>
        
        <label for="cena_netto">Cena netto:</label>
        <input type="number" step="0.01" name="cena_netto" value="' . $produkt['cena_netto'] . '" required><br><br>
        
        <label for="vat">Podatek VAT (%):</label>
        <input type="number" step="0.01" name="vat" value="' . $produkt['vat'] . '" required><br><br>
        
        <label for="ilosc">Ilość dostępnych sztuk:</label>
        <input type="number" name="ilosc" value="' . $produkt['ilosc'] . '" required><br><br>
        
        <label for="status">Status dostępności:</label>
        <select name="status">
            <option value="1"' . ($produkt['status'] ? ' selected' : '') . '>Dostępny</option>
            <option value="0"' . (!$produkt['status'] ? ' selected' : '') . '>Niedostępny</option>
        </select><br><br>
        
        <label for="kategoria">Kategoria:</label>
        <input type="text" name="kategoria" value="' . htmlspecialchars($produkt['kategoria']) . '"><br><br>
        
        <label for="gabaryt">Gabaryt:</label>
        <input type="text" name="gabaryt" value="' . htmlspecialchars($produkt['gabaryt']) . '"><br><br>
        
        <label for="zdjecie">Zdjęcie (link):</label>
        <input type="text" name="zdjecie" value="' . htmlspecialchars($produkt['zdjecie']) . '"><br><br>
        
        <button type="submit" name="zapisz_produkt">Zapisz zmiany</button>
    </form>';
}

// Obsługa edycji produktu
if (isset($_GET['edit_produkt'])) {
    EdytujProdukt(intval($_GET['edit_produkt']));
    exit;
}

// Obsługa zapisu zmian w produkcie
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['zapisz_produkt'])) {
    $id = $_POST['id'];
    $tytul = $_POST['tytul'];
    $opis = $_POST['opis'];
    $data_modyfikacji = date('Y-m-d');
    $data_wygasniecia = $_POST['data_wygasniecia'];
    $cena_netto = $_POST['cena_netto'];
    $vat = $_POST['vat'];
    $ilosc = $_POST['ilosc'];
    $status = $_POST['status'];
    $kategoria = $_POST['kategoria'];
    $gabaryt = $_POST['gabaryt'];
    $zdjecie = $_POST['zdjecie'];

    $query = "UPDATE produkty 
              SET tytul = ?, opis = ?, data_modyfikacji = ?, data_wygasniecia = ?, cena_netto = ?, vat = ?, ilosc = ?, status = ?, kategoria = ?, gabaryt = ?, zdjecie = ?
              WHERE id = ?";
    $stmt = $link->prepare($query);
    $stmt->bind_param("sssssdidisdi", $tytul, $opis, $data_modyfikacji, $data_wygasniecia, $cena_netto, $vat, $ilosc, $status, $kategoria, $gabaryt, $zdjecie, $id);
    $stmt->execute();
    $stmt->close();

    header('Location: admin.php');
    exit;
}

// Obsługa usuwania produktu
if (isset($_GET['delete_produkt'])) {
    $id = intval($_GET['delete_produkt']);
    $query = "DELETE FROM produkty WHERE id = ?";
    $stmt = $link->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header('Location: admin.php');
    exit;
}

// Wyświetlenie listy produktów
PokazProdukty();

// Formularz dodawania produktu
DodajProdukt();
?>
