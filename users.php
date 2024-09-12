<?php 
  session_start();
  include_once "php/config.php";
  if(!isset($_SESSION['unique_id'])){
    header("location: login.php");
  }

include_once "header.php"; 

?>
<body>
  <div class="wrapper">
    <section class="users">
      <header>
        <div class="content">
          <?php
          $user_id = $_SESSION['unique_id'];
            
          $stmt = $conn->prepare("SELECT * FROM users WHERE unique_id = ?");
          $stmt->bind_param("i", $user_id);
          $stmt->execute();
          $result = $stmt->get_result();
          $user_data = $result->fetch_assoc();
          ?>
          <img src="php/images/<?php echo $user_data['img']; ?>" alt="Avatar">
          <div class="details">
            <span><?php echo $user_data['firstname']. " " . $user_data['lastname'] ?></span>
            <p><?php echo $user_data['status']; ?></p>
          </div>
        </div>
        <div>
        <a href="show_profile.php" class="profile">Profilo</a>
        <a href="logout.php" class="logout">Logout</a>
        </div>
      </header>
      <div class="search">
        <span class="text">Seleziona una chat oppure cerca un utente</span>
        <input type="text" placeholder="Digita un nome...">
        <button><i class="fas fa-search"></i></button>
      </div>
      <div class="users-list">
  
      </div>
    </section>
  </div>

  <script src="javascript/users.js"></script>

</body>
</html>
