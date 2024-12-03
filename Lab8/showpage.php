<?php
function PokazPodstrone($id) {
    global $link;

    $id_clear = (int)$id;
    $query = "SELECT page_title, page_content FROM page_list WHERE id = $id_clear LIMIT 1";
    $result = mysqli_query($link, $query);
    $row = mysqli_fetch_assoc($result);

    if (!$row) {
        return ['title' => 'Strona nieznaleziona', 'content' => '[nie_znaleziono_strony]'];
    }

    ob_start();
    eval("?> " . $row['page_content'] . " <?php ");
    $content = ob_get_clean();

    return ['title' => $row['page_title'], 'content' => $content];
}

?>
