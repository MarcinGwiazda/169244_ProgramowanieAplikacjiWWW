<?php
$dbhost = 'localhost';
$dbuser = 'root';
$dbpass = '';
$dbbaza = 'moja_strona';

$login = 'marcin@gmail.com';
$pass = 'haslo';

$link = mysqli_connect($dbhost, $dbuser, $dbpass, $dbbaza);

if (!$link) {
    die("<b>Przerwane połączenie: </b>" . mysqli_connect_error());
}
?>
