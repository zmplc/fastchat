<?php include_once "header.php"; ?>
<body>

  <?php 
    session_start();
    if(isset($_SESSION['unique_id'])){
      header("location: users.php");
    }
  ?>

  <div class="wrapper">
    <section class="form signup">
      <header>FastChat - Registrazione</header>
      <form action="registration.php" method="POST" enctype="multipart/form-data" autocomplete="off">
        <?php if(isset($_GET['error'])) echo "<h5 class='error-text'>".$_GET['error']."</h5>"; ?>
        <div class="name-details">
          <div class="field input">
            <label for="firstname" class="form-label">Nome</label>
            <input type="text" name="firstname" id="firstname" placeholder="Nome" required>
          </div>
          <div class="field input">
            <label for="lastname" class="form-label">Cognome</label>
            <input type="text" name="lastname" id="lastname" placeholder="Cognome" required>
          </div>
        </div>
        <div class="field input">
          <label for="email" class="form-label">Email</label>
          <input type="text" name="email" id="email" placeholder="Inserisci la tua email" required>
        </div>
        <div class="field input">
          <label for="pass" class="form-label">Password (almeno 7 caratteri)</label>
          <input type="password" name="pass" id="pass" placeholder="Inserisci una nuova password" required>
        </div>
        <div class="field input">
          <label for="confirm" class="form-label">Conferma password</label>
          <input type="password" name="confirm" id="confirm" placeholder="Inserisci nuovamente la password" required>
        </div>
        <div class="field image">
          <label for="image" class="form-label">Seleziona il tuo avatar (facoltativo, dimensione massima 2MB)</label>
          <input type="file" name="image" id="image" accept="image/x-png,image/gif,image/jpeg,image/jpg">
        </div>
        <div class="field button">
          <input type="submit" name="submit" value="Vai alla Chat">
        </div>
      </form>
      <div class="link">Sei già registrato? <a href="login.php">Effettua il login!</a></div>
    </section>
  </div>

</body>
</html>

<?php
  
  if(!$_POST) exit();
  include_once "php/config.php";

  $firstname = $_POST['firstname'];
  $lastname = $_POST['lastname'];
  $email = $_POST['email'];
  $pass = $_POST['pass'];
  $confirmPass = $_POST['confirm'];

  // verifico se tutti i campi sono compilati
  if (!empty($firstname) && !empty($lastname) && !empty($email) && !empty($pass) && !empty($confirmPass)) {
    if (strlen($pass) <= 6) {
        header('location: registration.php?error=La password e\' troppo corta. Almeno 7 caratteri!');
        exit();
    }
    if ($pass !== $confirmPass) {
      header('location: registration.php?error=Le password non corrispondono.');
      exit;
    }
      
      if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
          $sql = "SELECT * FROM users WHERE email = ?";
          $stmt = $conn->prepare($sql);
          $stmt->bind_param("s", $email);
          $stmt->execute();
          $result = $stmt->get_result();
          
          if ($result->num_rows > 0) {
            header('location: registration.php?error=Questa email esiste gia\'.');
          } else {
              $new_img_name = "avatar.png"; // imposto l'avatar predefinito
              
              // se l'utente ha caricato una immagine la imposto come avatar
              if (isset($_FILES['image']) && $_FILES['image']['name'] != "") {
                  $img_name = $_FILES['image']['name'];
                  $img_type = $_FILES['image']['type'];
                  $tmp_name = $_FILES['image']['tmp_name'];

                  $img_explode = explode('.', $img_name);
                  $img_ext = end($img_explode);

                  $extensions = ["jpeg", "png", "jpg"];
                  if (in_array($img_ext, $extensions) === true) {
                      $types = ["image/jpeg", "image/jpg", "image/png"];
                      if (in_array($img_type, $types) === true) {
                          $time = time();
                          $new_img_name = $time . $img_name;
                          if (!move_uploaded_file($tmp_name, "images/" . $new_img_name)) {
                            header('location: registration.php?error=Errore durante il caricamento dell\'immagine');
                            exit;
                          }
                      } else {
                        header('location: registration.php?error=Carica un\'immagine con la seguente estensione: jpeg, png, jpg');
                        exit;
                      }
                  } else {
                    header('location: registration.php?error=Carica un\'immagine con la seguente estensione: jpeg, png, jpg');
                    exit;
                  }
              }

              // salvo i dati dell'utente nel database, uso ran_id per farlo diventare unique_id dell'utente da usare nelle altre pagine
              $ran_id = rand(time(), 100000000);
              $status = "Online";
              $hashedPass = password_hash($pass, PASSWORD_DEFAULT);
              $sql = "INSERT INTO users (unique_id, firstname, lastname, email, pass, img, status) VALUES (?, ?, ?, ?, ?, ?, ?)";
              $stmt = $conn->prepare($sql);
              $stmt->bind_param("issssss", $ran_id, $firstname, $lastname, $email, $hashedPass, $new_img_name, $status);
              // SELECT INUTILE, HO GIà I DATI
              if ($stmt->execute()) {
                  $sql = "SELECT * FROM users WHERE email = ?";
                  $stmt = $conn->prepare($sql);
                  $stmt->bind_param("s", $email);
                  $stmt->execute();
                  $result = $stmt->get_result();
                  if ($result->num_rows > 0) {
                      $user_data = $result->fetch_assoc();
                      $_SESSION['unique_id'] = $user_data['unique_id'];
                      header('location: users.php');
                  } else {
                    header('location: registration.php?error=Questa email non esiste.');
                  }
              } else {
                header('location: registration.php?error=Qualcosa è andato storto');
              }
          }
      } else {
        header('location: registration.php?error=L\' email che hai inserito non è valida.');
      }
  } else {
    header('location: registration.php?error=Devi compilare tutti i campi.');
  }

?>
