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
        <section class="show-profile">
            <?php
            $user_id = $_SESSION['unique_id'];
            
            $stmt = $conn->prepare("SELECT * FROM users WHERE unique_id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user_data = $result->fetch_assoc();
            ?>
                <div>
                    <header>
                        <div>
                            <h2>Il mio profilo</h2>
                        </div>
                        <div>
                            <a href="update_profile.php" class="update-profile">Modifica</a>
                            <a href="change_password.php" class="change-password">Cambio password</a>
                            <a href="logout.php" class="logout">Logout</a>
                        </div>
                    </header>
                    <div class="profile-info">
                        <img src="php/images/<?php echo $user_data['img']; ?>" alt="Avatar">
                        <p>Nome: <?php echo $user_data['firstname']; ?></p>
                        <p>Cognome: <?php echo $user_data['lastname']; ?></p>
                        <p>Email: <?php echo $user_data['email']; ?></p>
                        <p>Password: cambia la password <a href="change_password.php">cliccando qui!</a></p>
                        <p><a href="users.php">Torna alle chat</a></p>
                    </div>
                </div>
            <?php
            $stmt->close();
            ?>
        </section>
    </div>
</body>

</html>
