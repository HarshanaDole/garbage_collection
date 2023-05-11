<?php

include '../components/dbconfig.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

if (isset($_POST['update'])) {

    $id = $_POST['id'];
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $latitude = $_POST['latitude'];
    $latitude = filter_var($latitude, FILTER_SANITIZE_STRING);
    $longitude = $_POST['longitude'];
    $longitude = filter_var($longitude, FILTER_SANITIZE_STRING);
    $description = $_POST['description'];
    $description = filter_var($description, FILTER_SANITIZE_STRING);

    $update_product = $conn->prepare("UPDATE `incidents` SET name = ?, latitude = ?, longitude = ?, description = ? WHERE id = ?");
    $update_product->execute([$name, $latitude, $longitude, $description, $id]);

    $message[] = 'incident updated successfully!';

    $old_image = $_POST['old_image'];
    $image = $_FILES['image']['name'];
    $image = filter_var($image, FILTER_SANITIZE_STRING);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_img/' . $image;

    if (!empty($image)) {
        if ($image_size > 2000000) {
            $message[] = 'image size is too large!';
        } else {
            $update_image = $conn->prepare("UPDATE `incidents` SET image = ? WHERE id = ?");
            $update_image->execute([$image, $id]);
            move_uploaded_file($image_tmp_name, $image_folder);
            if (file_exists($old_image)) {
                unlink($old_image);
            }
            $message[] = 'image updated successfully!';
        }
    }


}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Report Incident</title>
    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/gtf.css">
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
</head>

<body>

    <?php include '../components/gtf_header.php'; ?>

    <div id="map"></div>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCu4iq-uhkrhk3_KHaCWRpBvsj-yZjBZfc"></script>
    <script>
        var map;
        var marker;

        function initMap() {
            // Set initial map center and zoom level
            var initialLocation = {
                lat: 0,
                lng: 0
            };
            map = new google.maps.Map(document.getElementById('map'), {
                center: {
                    lat: 6.9271,
                    lng: 79.8612
                },
                zoom: 10
            });

            // Get latitude and longitude values from the form
            var latitude = parseFloat(document.getElementById('latitude').value);
            var longitude = parseFloat(document.getElementById('longitude').value);

            // Set marker on incident location
            marker = new google.maps.Marker({
                position: {
                    lat: latitude,
                    lng: longitude
                },
                map: map
            });

            // Add click event listener to map
            google.maps.event.addListener(map, 'click', function(event) {
                // Set marker on click location
                if (marker) {
                    marker.setPosition(event.latLng);
                } else {
                    marker = new google.maps.Marker({
                        position: event.latLng,
                        map: map
                    });
                }

                // Fill in latitude and longitude fields
                document.getElementById('latitude').value = event.latLng.lat();
                document.getElementById('longitude').value = event.latLng.lng();
            });
        }
    </script>
    <script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCu4iq-uhkrhk3_KHaCWRpBvsj-yZjBZfc&callback=initMap"></script>

    <?php
    $update_id = $_GET['update'];
    $select_products = $conn->prepare("SELECT * FROM `incidents` WHERE id = ?");
    $select_products->execute([$update_id]);
    if ($select_products->rowCount() > 0) {
        while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
    ?>

            <form action="" method="post" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $fetch_products['id']; ?>">
                <input type="hidden" name="old_image" value="<?= $fetch_products['image_path']; ?>">
                <div class="form-row">
                    <div class="form-group">
                        <label for="latitude">Latitude:</label>
                        <input type="text" name="latitude" id="latitude" value="<?= $fetch_products['latitude']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="longitude">Longitude:</label>
                        <input type="text" name="longitude" id="longitude" value="<?= $fetch_products['longitude']; ?>" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" name="name" id="name" value="<?= $fetch_products['name']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea name="description" id="description"><?= $fetch_products['description']; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="image">Image:</label>
                    <input type="file" name="image" id="image">
                </div>

                <button type="submit" name="update">Update Incident</button>
            </form>

    <?php
        }
    } else {
        echo '<p class="empty">no incident found!</p>';
    }
    ?>
</body>

</html>