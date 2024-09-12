<?php
session_start();
include_once "config.php";

if(!isset($_SESSION['unique_id'])){
    header("location: login.php");
    exit();
}

// recupero id utente e il search term
$outgoing_id = $_SESSION['unique_id'];
$searchTerm = "%" . $_POST['searchTerm'] . "%"; // per la ricerca dell'utente

$output = "";

// query per cercare l'utente con il nome/cognome cercato
$sql = "SELECT * FROM users WHERE unique_id != ? AND (firstname LIKE ? OR lastname LIKE ?)";
$stmt = $conn->prepare($sql);

if ($stmt) {
    // bind dei parametri
    $stmt->bind_param("iss", $outgoing_id, $searchTerm, $searchTerm);

    $stmt->execute();
    $result = $stmt->get_result();

    // controllo se ci sono utenti con questo nome/cognome
    if ($result->num_rows > 0) {
        // ciclo per costruire output per ogni utente trovato
        while ($row = $result->fetch_assoc()) {
            // genero token unico bassato su unique_id per non mostrare unique_id nell'URL
            $token = base64_encode($row['unique_id']);

            // controllo lo stato dell'utente per mostrare pallino verde(online) pallino grigio(offline)
            $offline = ($row['status'] == "Offline") ? "offline" : "";

            // output per ogni utente
            $output .= '<a href="chat.php?user=' . $token . '">
                        <div class="content">
                            <img src="php/images/' . $row['img'] . '" alt="Avatar">
                            <div class="details">
                                <span>' . $row['firstname'] . ' ' . $row['lastname'] . '</span>
                                <p>' . $row['status'] . '</p>
                            </div>
                        </div>
                        <div class="status-dot ' . $offline . '"><i class="fas fa-circle"></i></div>
                    </a>';
        }
    } else {
        $output .= 'Nessun utente trovato con questo nome, riprova con un altro.';
    }

    // chiudo statement
    $stmt->close();
} else {
    // errore query
    $output .= "Errore nella query: " . $conn->error;
}

// echo dell'output della ricerca
echo $output;
?>
