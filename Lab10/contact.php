<?php
// Funkcja wyświetlająca formularz kontaktowy
function PokazKontakt() {
    return '
    <form method="post" action="">
        <h2>Formularz kontaktowy</h2>
        <label for="temat">Temat:</label><br>
        <input type="text" id="temat" name="temat" required><br><br>
        
        <label for="email">Twój Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>
        
        <label for="tresc">Treść wiadomości:</label><br>
        <textarea id="tresc" name="tresc" rows="6" cols="40" required></textarea><br><br>
        
        <input type="submit" name="wyslij" value="Wyślij wiadomość">
    </form>';
}

function WyslijMailKontakt($odbiorca) {
    // Sprawdzenie, czy pola formularza zostały wypełnione
    if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
        echo '[nie_wypelniles_pola]';
        echo PokazKontakt(); // Ponowne wyświetlenie formularza
    } else {
        // Pobranie danych z formularza
        $mail['subject'] = $_POST['temat'];
        $mail['body'] = $_POST['tresc'];
        $mail['sender'] = $_POST['email'];
        $mail['recipient'] = $odbiorca; // Adres odbiorcy przekazywany jako argument funkcji

        // Tworzenie nagłówków maila
        $header = "From: Formularz kontaktowy <" . $mail['sender'] . ">\r\n";
        $header .= "MIME-Version: 1.0\r\n";
        $header .= "Content-Type: text/plain; charset=utf-8\r\n";
        $header .= "Content-Transfer-Encoding: 8bit\r\n";
        $header .= "X-Priority: 3\r\n";
        $header .= "Return-Path: <" . $mail['sender'] . ">\r\n";

        // Wysyłanie maila
        $mail_sent = mail($mail['recipient'], $mail['subject'], $mail['body'], $header);

        // Komunikat o wyniku wysyłania
        if ($mail_sent) {
            echo '[wiadomosc_wyslana]';
        } else {
            echo '[blad_wysylania_wiadomosci]';
        }
    }
}


// Funkcja do przypomnienia hasła admina
function PrzypomnijHaslo() {
    $admin_email = "marcin@gmail.com"; // Email administratora
    $admin_password = "haslo"; // Przykładowe hasło
    
    $subject = "Przypomnienie hasła - Panel CMS";
    $body = "Twoje hasło do panelu administracyjnego to: " . $admin_password;
    $header = "From: Panel CMS <no-reply@twojadomena.pl>\r\n";
    $header .= "MIME-Version: 1.0\r\n";
    $header .= "Content-Type: text/plain; charset=utf-8\r\n";
    $header .= "Content-Transfer-Encoding: 8bit\r\n";

    // Wysyłanie maila z hasłem
    $mail_sent = mail($admin_email, $subject, $body, $header);

    if ($mail_sent) {
        echo '<p>[Przypomnienie hasła wysłane]</p>';
    } else {
        echo '<p>[Błąd podczas wysyłania przypomnienia]</p>';
    }
}

// Obsługa formularza w zależności od akcji
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['wyslij'])) {
        WyslijMailKontakt('m.gwiazda27@gmail.com'); // Wysłanie wiadomości z formularza kontaktowego
    } elseif (isset($_POST['przypomnij_haslo'])) {
        PrzypomnijHaslo(); // Wysłanie maila z przypomnieniem hasła
    }
} else {
    echo PokazKontakt(); // Domyślnie pokazuje formularz kontaktowy
}
?>



