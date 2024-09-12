<?php 
session_start();
if(isset($_SESSION['unique_id'])){
    include_once "config.php";
    $outgoing_id = $_SESSION['unique_id'];
    $incoming_id = $_POST['incoming_id'];
    $output = "";

    // query per prendere i messaggi della chat
    $sql = "SELECT messages.msg, users.img, messages.outgoing_msg_id 
            FROM messages 
            LEFT JOIN users ON users.unique_id = messages.outgoing_msg_id
            WHERE (outgoing_msg_id = ? AND incoming_msg_id = ?)
            OR (outgoing_msg_id = ? AND incoming_msg_id = ?)
            ORDER BY msg_id";
    // preparo lo statement
    $stmt = $conn->prepare($sql);
    // parametri
    $stmt->bind_param("iiii", $outgoing_id, $incoming_id, $incoming_id, $outgoing_id);
    //eseguo lo statement
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            if($row['outgoing_msg_id'] === $outgoing_id){
                $output .= '<div class="chat outgoing">
                            <div class="details">
                                <p>'. htmlspecialchars($row['msg'], ENT_QUOTES, 'UTF-8') .'</p>
                            </div>
                            </div>';
            } else {
                $output .= '<div class="chat incoming">
                            <img src="php/images/'. htmlspecialchars($row['img'], ENT_QUOTES, 'UTF-8') .'" alt="">
                            <div class="details">
                                <p>'. htmlspecialchars($row['msg'], ENT_QUOTES, 'UTF-8') .'</p>
                            </div>
                            </div>';
            }
        }
    } else {
        $output .= '<div class="text">Nessun messaggio. Comincia a chattare!</div>';
    }
    echo $output;

    $stmt->close();
    $conn->close();
} else {
    header("location: ../login.php");
    exit();
}
?>
