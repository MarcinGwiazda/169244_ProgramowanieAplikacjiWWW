<?php
// Ustawienia bazy danych
$dbhost = 'localhost';  // Host bazy danych
$dbuser = 'root';       // Użytkownik bazy danych
$dbpass = '';           // Hasło do bazy danych
$dbbaza = 'moja_strona'; // Nazwa bazy danych

// Dane logowania do panelu administracyjnego
$login = 'marcin@gmail.com'; // Login admina
$pass = 'haslo';             // Hasło admina

// Połączenie z bazą danych
$link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbbaza);

// Sprawdzenie, czy połączenie zostało nawiązane
if (!$link) {
    // Przerwanie skryptu w przypadku błędu połączenia
    die("<b>Przerwane połączenie: </b>" . mysqli_connect_error());
}

?>
