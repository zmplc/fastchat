<?php
    session_start();
    if(!isset($_SESSION['unique_id'])){
        // utente non loggato
        header("location: login.php");
        exit(); // termino script
    }

    include_once "php/config.php";
        $logout_id = $_SESSION['unique_id'];
        $status = "Offline";
        // preparo lo statement
        $stmt = $conn->prepare("UPDATE users SET status = ? WHERE unique_id = ?");
        // associo i parametri
        $stmt->bind_param("si", $status, $logout_id);
        // eseguo lo statement
        $stmt->execute();
        // unsetto la sessione
        if($stmt->affected_rows > 0){
            // unset e destroy della sessione
            unset($_SESSION['unique_id']);
            session_destroy();
            // reindirizzo l'utente al login
            header("location: login.php");
            exit(); // termino script
        } else {
            // errore durante il logout
            echo "Errore durante il logout.";
            exit(); // termino script
        }
        // chiudo lo statement
        $stmt->close();

?>
