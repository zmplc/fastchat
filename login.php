<?php 
  session_start();
  if(isset($_SESSION['unique_id'])){
    header("location: users.php");
  }
?>

<?php include_once "header.php"; ?>
<body>
  <div class="wrapper">
    <section class="form login">
      <header>FastChat - Login</header>
      <form action="login.php" method="POST" enctype="multipart/form-data" autocomplete="off">
        <?php if(isset($_GET['error'])) echo "<h5 class='error-text'>".$_GET['error']."</h5>"; ?>
        <div class="field input">
          <label>Email</label>
          <input type="text" name="email" placeholder="Inserisci la tua email" required>
        </div>
        <div class="field input">
          <label>Password</label>
          <input type="password" name="pass" placeholder="Inserisci la tua password" required>
        </div>
        <div class="field button">
          <input type="submit" name="submit" value="Vai alla Chat">
        </div>
      </form>
      <div class="link">Non sei ancora registrato? <a href="registration.php">Registrati ora!</a></div>
    </section>
  </div>

</body>
</html>

<?php 

  if(!$_POST) exit();
  include_once "php/config.php";

  $email = $_POST['email'];
  $pass = $_POST['pass'];

  if (!empty($email) && !empty($pass)) {
      $sql = "SELECT * FROM users WHERE email = ?";
      $stmt = $conn->prepare($sql);
      $stmt->bind_param("s", $email);
      $stmt->execute();
      $result = $stmt->get_result();

      if ($result->num_rows > 0) {
          $row = $result->fetch_assoc();
          $hashedPass = $row['pass'];

          if (password_verify($pass, $hashedPass)) {
              $status = "Online";
              $sql2 = "UPDATE users SET status = ? WHERE unique_id = ?";
              $stmt2 = $conn->prepare($sql2);
              $stmt2->bind_param("si", $status, $row['unique_id']);
              if ($stmt2->execute()) {
                $_SESSION['unique_id'] = $row['unique_id'];
                header('location: users.php');
              } else {
                header('location: login.php?error=Qualcosa e\' andato storto.');
              }
          } else {
            header('location: login.php?error=Email e password non corrette.');
          }
      } else {
        header('location: login.php?error=Questa email non esiste.');
      }
  } else {
      header('location: login.php?error=Ci servono tutti i campi compilati.');
  }
?>

