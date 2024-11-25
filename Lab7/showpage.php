<?php
function PokazPodstrone($id) {
    global $link;

    $id_clear = (int)$id; 
    $query = "SELECT page_title AS title, page_content AS content FROM page_list WHERE id = $id_clear LIMIT 1";
    $result = mysqli_query($link, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return [
            'title' => $row['title'],
            'content' => $row['content']
        ];
    } else {
        return ['content' => '[nie_znaleziono_strony]'];
    }
}

?>
