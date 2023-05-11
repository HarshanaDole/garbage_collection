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

        <a href="home.php" class="logo"><img src="../images/green-task-force-logo-lean-crop.png" alt="GTF logo"></a>

        <nav class="navbar">
            <a href="home.php">Home</a>
            <a href="report_incidents.php">Report incidents</a>
            <a href="my_incidents.php">My incidents</a>
            <a href="contact.php">Contact</a>
            <a href="about.php">About</a>
        </nav>

        <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <a href="search_page.php"><i class="fas fa-search"></i></a>&nbsp;
            <div id="user-btn" class="fas fa-user"></div>
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