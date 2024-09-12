<?php 
session_start();
if(!isset($_SESSION['unique_id'])){
    header("location: ../login.php");
    exit();
}

include_once "config.php";
$outgoing_id = $_SESSION['unique_id'];
$incoming_id = $_POST['incoming_id'];
$message = $_POST['message'];
    
if(!empty($message)){
    // query
    $sql = "INSERT INTO messages (incoming_msg_id, outgoing_msg_id, msg) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
        
    if($stmt) {
        // parametri
        $stmt->bind_param("iis", $incoming_id, $outgoing_id, $message);
            
        // eseguo query
        if($stmt->execute()) {
            // messaggio inserito ok
        } else {
            // errore nello statement
            die("Errore nell'inserimento del messaggio: " . $stmt->error);
        }
            
        // chiudo statement
        $stmt->close();
    } else {
        // errore nella query
        die("Errore nella query: " . $conn->error);
    }
}

?>
