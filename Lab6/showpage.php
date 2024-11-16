<?php
function PokazPodstrone($id) {
    global $link;

    $id_clear = (int)$id; 

    $query = "SELECT * FROM page_list WHERE id=$id_clear LIMIT 1";
    $result = mysqli_query($link, $query);

    if (!$result) {
        return '[Błąd w zapytaniu SQL]';
    }

    $row = mysqli_fetch_array($result);

    if (empty($row['id'])) {
        $web = '[nie_znaleziono_strony]';
    } else {
        $web = $row['page_content'];
    }

    return $web;
}
?>
