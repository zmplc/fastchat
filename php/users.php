<?php
session_start();
include_once "config.php";
$outgoing_id = $_SESSION['unique_id'];
$output = "";

// query
$sql = "SELECT * FROM users WHERE NOT unique_id = ? ORDER BY user_id DESC";
$stmt = $conn->prepare($sql);

if($stmt) {
    // parametro
    $stmt->bind_param("i", $outgoing_id);
    // esecuzione statement
    $stmt->execute();
    $result = $stmt->get_result();

    // controllo utenti disponibili (nel caso in cui l'utente sia l'unico registrato)
    if($result->num_rows == 0){
        $output .= "Nessun utente disponibile per chattare.";
    } else {
        // salvo i risultati perché li devo usare in data.php
        $query = $result;
        include_once "data.php";
    }
    // chiudo statement
    $stmt->close();
} else {
    // errore nella query
    $output .= "Errore nella query: " . $conn->error;
}

// mostro l'output nella pagina
echo $output;

?>
