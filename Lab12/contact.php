<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Dodanie PHPMailer przez Composer
require 'vendor/autoload.php';

// Funkcja wyświetlająca formularz kontaktowy
function PokazKontakt() {
    return '
    <h2>Formularz kontaktowy</h2>
    <form method="post" action="" id="kontaktForm">
        <label for="temat">Temat:</label><br>
        <input type="text" id="temat" name="temat" required placeholder="Wpisz temat wiadomości"><br><br>
        
        <label for="email">Twój Email:</label><br>
        <input type="email" id="email" name="email" required placeholder="example@mail.com"><br><br>
        
        <label for="tresc">Treść wiadomości:</label><br>
        <textarea id="tresc" name="tresc" rows="6" cols="40" required placeholder="Wpisz swoją wiadomość..."></textarea><br><br>
        
        <input type="submit" name="wyslij" value="Wyślij wiadomość" class="button">
        <input type="submit" name="przypomnij_haslo" value="Przypomnij hasło" class="button button-secondary" onclick="usunRequired()">
    </form>

    <br><a href="admin/admin.php" class="admin-button">Przejdź do panelu administracyjnego</a>

    <script>
        function usunRequired() {
            document.getElementById("temat").removeAttribute("required");
            document.getElementById("email").removeAttribute("required");
            document.getElementById("tresc").removeAttribute("required");
        }
    </script>
    ';
}

//  Funkcja wysyłania wiadomości przez PHPMailer
function WyslijMailKontakt($odbiorca) {
    if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
        echo '[nie_wypelniles_pola]';
        echo PokazKontakt();
    } else {
        $mail = new PHPMailer(true);

        try {
            // Konfiguracja serwera SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'm.gwiazda27@gmail.com';
            $mail->Password   = 'gycg qthq tdwr hjfx'; 
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; 
            $mail->Port       = 587;

            // Dane nadawcy i odbiorcy
            $mail->setFrom($_POST['email'], 'Formularz Kontaktowy');
            $mail->addAddress($odbiorca);  

            // Treść wiadomości
            $mail->isHTML(true);
            $mail->Subject = $_POST['temat'];
            $mail->Body    = nl2br($_POST['tresc']);
            $mail->AltBody = $_POST['tresc'];

            $mail->send();
            echo '<p>[Wiadomość została wysłana]</p>';
        } catch (Exception $e) {
            echo '<p>[Błąd wysyłania wiadomości]: ' . $mail->ErrorInfo . '</p>';
        }
    }
}

//  Funkcja do przypomnienia hasła admina przez PHPMailer
function PrzypomnijHaslo() {
    $admin_email = "marcin@gmail.com"; 
    $admin_password = "haslo";

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'm.gwiazda27@gmail.com';
        $mail->Password   = 'gycg qthq tdwr hjfx';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('m.gwiazda27@gmail.com', 'Panel CMS');
        $mail->addAddress('m.gwiazda27@gmail.com');

        $mail->isHTML(true);
        $mail->Subject = "Przypomnienie hasla - Panel CMS";
        $mail->Body    = "Twoje hasło do panelu administracyjnego to: <b>{$admin_password}</b>";
        $mail->AltBody = "Twoje hasło do panelu administracyjnego to: {$admin_password}";

        $mail->send();
        echo '<p>[Przypomnienie hasła wysłane]</p>';
    } catch (Exception $e) {
        echo '<p>[Błąd podczas wysyłania przypomnienia]: ' . $mail->ErrorInfo . '</p>';
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['wyslij'])) {
        // Walidacja tylko dla wysyłki wiadomości
        if (empty($_POST['temat']) || empty($_POST['tresc']) || empty($_POST['email'])) {
            echo '[Nie wypełniłeś wszystkich wymaganych pól]';
            echo PokazKontakt();
        } else {
            WyslijMailKontakt('m.gwiazda27@gmail.com');
        }
    } elseif (isset($_POST['przypomnij_haslo'])) {
        // Przypomnienie hasła NIE wymaga walidacji pól
        PrzypomnijHaslo();
    }
} else {
    echo PokazKontakt();
}
?>
