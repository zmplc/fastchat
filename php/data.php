<?php
include_once "config.php";

if(!isset($_SESSION['unique_id'])){
    header("location: login.php");
    exit();
}

// recupero unique_id utente
$outgoing_id = $_SESSION['unique_id'];

// query per prendere i dati per costruire la lista utenti
$sql = "SELECT * FROM users WHERE unique_id != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $outgoing_id);
$stmt->execute();
$query = $stmt->get_result();

$output = "";
while($row = $query->fetch_assoc()){
    // genero token unico bassato su unique_id per non mostrare unique_id nell'URL
    $token = base64_encode($row['unique_id']);

    // controllo lo stato dell'utente per mostrare pallino verde(online) pallino grigio(offline)
    $offline = ($row['status'] == "Offline") ? "offline" : "";

    // output per lista utenti
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
?>