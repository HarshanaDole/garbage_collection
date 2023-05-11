<?php
if (isset($message)) {
    foreach ($message as $msg) {
        echo '
         <div class="message">
            <span>' . $msg . '</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
         </div>
         ';
    }
}
?>

<header class="header">

    <section class="flex">

        <a href="home.php" class="logo"><img src="images/green-captain-logo-cropped.png" alt="GTF logo"></a>

        <nav class="navbar">
            <a href="home.php">Home</a>
            <a href="approved_incidents.php">Approved incidents</a>
            <a href="profile.php">My Profile</a>
        </nav>

        <div class="icons">
        </div>

        <div class="profile">
            <?php
            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
            $select_profile->execute([$user_id]);
            if ($select_profile->rowCount() > 0) {
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
            ?>
                <p><?= $fetch_profile["username"]; ?></p>
                <a href="update_user.php" class="btn">update profile</a>
                <div class="flex-btn">
                    <a href="user_register.php" class="option-btn">register</a>
                    <a href="user_login.php" class="option-btn">login</a>
                </div>
                <a href="components/user_logout.php" class="delete-btn" onclick="return confirm('logout from the website?');">logout</a>
            <?php
            } else {
            ?>
                <p>please login or register first!</p>
                <div class="flex-btn">
                    <a href="user_register.php" class="option-btn">register</a>
                    <a href="user_login.php" class="option-btn">login</a>
                </div>
            <?php
            }
            ?>


        </div>

    </section>

</header>