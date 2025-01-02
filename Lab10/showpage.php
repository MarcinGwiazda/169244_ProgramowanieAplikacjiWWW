<?php
/**
  Funkcja PokazPodstrone
  Pobiera dane podstrony (tytuł i treść) z bazy danych na podstawie jej ID.
  Obsługuje dynamiczne generowanie treści za pomocą eval() i buforowania wyjścia.
 */
function PokazPodstrone($id) {
    global $link; // Użycie globalnego połączenia z bazą danych

    // Zabezpieczenie ID przez konwersję na liczbę całkowitą
    $id_clear = (int)$id;

    // Przygotowanie zapytania SQL z ograniczeniem do jednej podstrony
    $query = "SELECT page_title, page_content FROM page_list WHERE id = $id_clear LIMIT 1";
    $result = mysqli_query($link, $query); // Wykonanie zapytania
    $row = mysqli_fetch_assoc($result); // Pobranie wyników w postaci tablicy asocjacyjnej

    // Jeśli podstrona nie zostanie znaleziona, zwracany jest komunikat o błędzie
    if (!$row) {
        return [
            'title' => 'Strona nieznaleziona', 
            'content' => '[nie_znaleziono_strony]'
        ];
    }

    // Dynamiczne generowanie treści podstrony
    ob_start(); // Rozpoczęcie buforowania wyjścia
    eval("?> " . $row['page_content'] . " <?php "); // Wykonanie kodu PHP zawartego w treści strony
    $content = ob_get_clean(); // Pobranie zawartości bufora i zakończenie buforowania

    // Zwracanie danych podstrony
    return [
        'title' => $row['page_title'], 
        'content' => $content
    ];
}
?>
