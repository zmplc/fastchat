<?php 
session_start();
include_once "php/config.php";

if(!isset($_SESSION['unique_id'])){
    header("location: login.php");
    exit();
}

include_once "header.php"; 
?>
<body>
  <div class="wrapper">
    <section class="chat-area">
      <header>
      <?php
      // Recupera l'ID dell'utente autenticato
      $outgoing_id = $_SESSION['unique_id'];

      // Verifica se è stato passato un ID utente nella richiesta e se è valido
      if(isset($_GET['user'])) {
          // Decodifica il token per ottenere l'ID dell'utente
          $user_id = base64_decode($_GET['user']);
          $user_id = intval($user_id);

          // Verifica se l'ID utente è valido
          if($user_id <= 0) {
              die("Utente non valido.");
          }
      } else {
          die("ID utente non fornito.");
      }

      // Prepara la query per ottenere le informazioni dell'utente
      $stmt = $conn->prepare("SELECT * FROM users WHERE unique_id = ?");
      $stmt->bind_param("i", $user_id);
      $stmt->execute();
      $result = $stmt->get_result();
      if($result->num_rows > 0){
          $user_data = $result->fetch_assoc();
      } else {
          die("Utente non trovato.");
      }
      ?>
        <a href="users.php" class="back-icon"><i class="fas fa-arrow-left"></i></a>
        <img src="php/images/<?php echo $user_data['img']; ?>" alt="Avatar">
        <div class="details">
          <span><?php echo $user_data['firstname']. " " . $user_data['lastname']; ?></span>
          <p><?php echo $user_data['status']; ?></p>
        </div>
      </header>
      <div class="chat-box">

      </div>
      <form action="#" class="typing-area">
        <input type="text" class="incoming_id" name="incoming_id" value="<?php echo $user_id; ?>" hidden>
        <input type="text" name="message" class="input-field" placeholder="Scrivi un messaggio..." autocomplete="off">
        <button><i class="fab fa-telegram-plane"></i></button>
      </form>
    </section>
  </div>

  <script src="javascript/chat.js"></script>

</body>
</html>
