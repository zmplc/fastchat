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
            <header>Modifica il tuo profilo</header>
            <form action="update_profile.php" method="POST" enctype="multipart/form-data" autocomplete="off">
            <?php if(isset($_GET['error'])) echo "<h5 class='error-text'>".$_GET['error']."</h5>"; ?>

                <div class="field input">
                    <label for="firstname" class="form-label">Nome</label>
                    <input type="text" name="firstname" id="firstname" placeholder="Modifica il tuo nome">
                </div>

                <div class="field input">
                    <label for="lastname" class="form-label">Cognome</label>
                    <input type="text" name="lastname" id="lastname" placeholder="Modifica il tuo cognome">
                </div>

                <div class="field input">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" name="email" id="email" placeholder="Modifica la tua email">
                </div>

                <div class="field image">
                    <label for="image" class="form-label">Modifica il tuo avatar (facoltativo)</label>
                    <input type="file" name="image" id="image" accept="image/x-png,image/jpeg,image/jpg">
                </div>

                <div class="field button">
                    <input type="submit" name="submit" value="Salva Modifiche Profilo">
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
    session_start();
    include_once "php/config.php";

    $outgoing_id = $_SESSION['unique_id'];

    $firstname = !empty($_POST['firstname']) ? $_POST['firstname'] : null;
    $lastname = !empty($_POST['lastname']) ? $_POST['lastname'] : null;
    $email = !empty($_POST['email']) ? $_POST['email'] : null;

    // recupero dal db i dati dell'utente
    $sql = "SELECT firstname, lastname, email, img FROM users WHERE unique_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $outgoing_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // se campi vuoti allora mantengo i dati dell'account
        $firstname = $firstname ?: $row['firstname'];
        $lastname = $lastname ?: $row['lastname'];
        $current_email = $row['email'];
        $img_name = $row['img'];
    } else {
        header('location: update_profile.php?error=Errore nel recupero dei tuoi dati.');
        exit();
    }

    // controllo se l'email è già usata da un altro account nel db (primo check se nel campo c'è una nuova email, secondo check per vedere se l'email è diversa da quella presente nel db)
    if ($email && $email !== $current_email) {
        $sql_email_check = "SELECT email FROM users WHERE email = ? AND unique_id != ?";
        $stmt_email_check = $conn->prepare($sql_email_check);
        $stmt_email_check->bind_param("si", $email, $outgoing_id);
        $stmt_email_check->execute();
        $result_email_check = $stmt_email_check->get_result();

        if ($result_email_check->num_rows > 0) {
            header('location: update_profile.php?error=L\'email e\' gia\' registrara da un altro account.');
        exit();
        }
        $stmt_email_check->close();
    } else {
        // se non ci sono modifiche all'email lascio quella che c'era nel db
        $email = $current_email; 
    }

    // se viene caricata una nuova immagine uso stesso script della registrazione
    if (!empty($_FILES['image']['name'])) {
        $img_name = $_FILES['image']['name'];
        $img_type = $_FILES['image']['type'];
        $tmp_name = $_FILES['image']['tmp_name'];

        $img_explode = explode('.', $img_name);
        $img_ext = end($img_explode);

        $extensions = ["jpeg", "png", "jpg"];
        if (in_array($img_ext, $extensions)) {
            $types = ["image/jpeg", "image/jpg", "image/png"];
            if (in_array($img_type, $types)) {
                $time = time();
                $new_img_name = $time . $img_name;
                if (move_uploaded_file($tmp_name, "images/" . $new_img_name)) {
                    $img_name = $new_img_name;
                } else {
                    header('location: update_profile.php?error=Errore nel caricamente dell\'immagine.');
                    exit();
                }
            } else {
                header('location: update_profile.php?error=Carica un\'immagine valida, verifica le estensioni: (jpeg, jpg, png)');
                exit();
            }
        } else {
            header('location: update_profile.php?error=Carica un\'immagine valida, verifica le estensioni: (jpeg, jpg, png)');
            exit();
        }
    }

    // aggiorno i dati
    $sql2 = "UPDATE users SET firstname = ?, lastname = ?, email = ?, img = ? WHERE unique_id = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("ssssi", $firstname, $lastname, $email, $img_name, $outgoing_id);

    // verifico se l'aggiornamento è andato ok, faccio echo "success" e lo script js mi porta alla pagina show_profile.php
    if ($stmt2->execute()) {
        header('location: show_profile.php');
    } else {
        header('location: update_profile.php?error=Errore nell\'aggiornamento del profilo.');
        exit();
    }

    $stmt->close();
    $stmt2->close();

?>
