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
        <section class="form update-profile">
            <header>Cambia la tua password</header>
            <form action="change_password.php" method="POST" autocomplete="off">
                <?php if(isset($_GET['error'])) echo "<h5 class='error-text'>".$_GET['error']."</h5>"; ?>

                <div class="field input">
                    <label for="current_password" class="form-label">Password corrente</label>
                    <input type="password" name="current_pass" id="current_pass" placeholder="Inserisci la tua password" required>
                </div>

                <div class="field input">
                    <label for="new_password" class="form-label">Nuova password (almeno 7 caratteri)</label>
                    <input type="password" name="new_pass" id="new_pass" placeholder="Inserisci la nuova password" required>
                </div>

                <div class="field input">
                    <label for="confirm_password" class="form-label">Conferma nuova password</label>
                    <input type="password" name="confirm_new_pass" id="confirm_new_pass" placeholder="Conferma la nuova password" required>
                </div>

                <div class="field button">
                    <input type="submit" name="submit" value="Cambia Password">
                </div>
            </form>
            <div class="link">
                <a href="show_profile.php">Torna al tuo profilo</a>
            </div>
        </section>
    </div>

</body>

</html>

<?php
  
    if(!$_POST) exit();
    include_once "php/config.php";

    $outgoing_id = $_SESSION['unique_id'];
    $current_pass = $_POST['current_pass'];
    $new_pass = $_POST['new_pass'];
    $confirm_new_pass = $_POST['confirm_new_pass'];

    // verifico se tutti i campi sono completi
    if (empty($current_pass) || empty($new_pass) || empty($confirm_new_pass)) {
        header('location: change_password.php?error=Tutti i campi sono obbligatori!');
        exit();
    }

    // verifico che la nuova password abbia almeno 7 caratteri
    if (strlen($new_pass) <= 6) {
        header('location: change_password.php?error=La password e\' troppo corta. Almeno 7 caratteri!');
        exit();
    }

    // verifico se la nuova password è = alla conferma
    if ($new_pass !== $confirm_new_pass) {
        header('location: change_password.php?error=La nuova password e la conferma non corrispondono.');
        exit();
    }

    // recupero la pass dal db usando unique_id dell'utente
    $sql = "SELECT pass FROM users WHERE unique_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $outgoing_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashed_password = $row['pass'];

        // verifico se la password corrente corrisponde a quella che c'è nel db
        if (password_verify($current_pass, $hashed_password)) {

            // faccio l'hash della nuova password
            $new_hashed_pass = password_hash($new_pass, PASSWORD_DEFAULT);

            // aggiorno la password nel db
            $sql2 = "UPDATE users SET pass = ? WHERE unique_id = ?";
            $stmt2 = $conn->prepare($sql2);
            $stmt2->bind_param("si", $new_hashed_pass, $outgoing_id);

            if ($stmt2->execute()) {
                header('location: show_profile.php');
            } else {
                header('location: change_password.php?error=Errore durante l\'aggiornamento della password.');
            }

            $stmt2->close();
        } else {
            header('location: change_password.php?error=La password attuale non è corretta.');
        }
    } else {
        header('location: change_password.php?error=Errore nel recupero dei tuoi dati.');
    }

    $stmt->close();
?>
