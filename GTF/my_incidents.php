<?php

include '../components/dbconfig.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
};

if (isset($_GET['delete'])) {

    $delete_id = $_GET['delete'];
    $delete_incident_image = $conn->prepare("SELECT * FROM `incidents` WHERE id = ?");
    $delete_incident_image->execute([$delete_id]);
    $fetch_delete_image = $delete_incident_image->fetch(PDO::FETCH_ASSOC);
    unlink($fetch_delete_image['image_path']);
    $delete_incident = $conn->prepare("DELETE FROM `incidents` WHERE id = ?");
    $delete_incident->execute([$delete_id]);
    header('location:my_incidents.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/gtf.css">
</head>

<body>
<?php include '../components/gtf_header.php'; ?>
    <section class="show-incidents">

        <h1 class="heading">reported incidents</h1>

        <div class="box-container">

            <?php
            $select_incidents = $conn->prepare("SELECT * FROM `incidents` WHERE user_id = ?");
            $select_incidents->execute([$user_id]);
            if ($select_incidents->rowCount() > 0) {
                while ($fetch_incidents = $select_incidents->fetch(PDO::FETCH_ASSOC)) {
            ?>
                    <div class="box">
                        <img src="<?= $fetch_incidents['image_path']; ?>" alt="">
                        <div class="location-container">
                            <div class="name">Latitude:</div>
                            <div class="value"><?= $fetch_incidents['latitude']; ?></div>
                        </div>
                        <div class="location-container">
                            <div class="name">Longitude:</div>
                            <div class="value"><span><?= $fetch_incidents['longitude']; ?></span></div>
                        </div>
                        <div class="description"><span><?= $fetch_incidents['description']; ?></span></div>
                        <div class="flex-btn">
                            <a href="update_incident.php?update=<?= $fetch_incidents['id']; ?>" class="option-btn">update</a>
                            <a href="my_incidents.php?delete=<?= $fetch_incidents['id']; ?>" class="delete-btn" onclick="return confirm('delete this incident?');">delete</a>
                        </div>
                    </div>
            <?php
                }
            } else {
                echo '<p class="empty">no incidents added yet!</p>';
            }
            ?>

        </div>

    </section>

</body>

</html>