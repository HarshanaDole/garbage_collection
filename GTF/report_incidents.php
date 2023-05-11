<?php

include '../components/dbconfig.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:login.php');
}

if (isset($_POST['submit'])) {
    // Get the form data
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $image_path = '';

    // Upload the image
    if ($_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image_path = 'uploads/' . uniqid() . '-' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
    }

    // Check if the incident already exists in the database
    try {
        $conn = new PDO($db_name, $user_name, $user_password);
        $query = "SELECT * FROM incidents WHERE latitude=? AND longitude=?";
        $stmt = $conn->prepare($query);
        $stmt->execute([$latitude, $longitude]);
        $result = $stmt->fetch();
        if ($result) {
            $message[] = 'Incident with these coordinates already exists in the database.';
        } else {
            // Insert the incident into the database
            $query = "INSERT INTO incidents (latitude, longitude,name, description, image_path, user_id) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->execute([$latitude, $longitude, $name, $description, $image_path, $user_id]);
            $message[] = 'Incident reported successfully!';
        }
    } catch (PDOException $e) {
        $message[] = 'Error reporting incident: ' . $e->getMessage();
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
    <form action="" method="post" enctype="multipart/form-data">
        <div class="form-row">
            <div class="form-group">
                <label for="latitude">Latitude:</label>
                <input type="text" name="latitude" id="latitude" required>
            </div>
            <div class="form-group">
                <label for="longitude">Longitude:</label>
                <input type="text" name="longitude" id="longitude" required>
            </div>
        </div>
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea name="description" id="description"></textarea>
        </div>

        <div class="form-group">
            <label for="image">Image:</label>
            <input type="file" name="image" id="image" required>
        </div>

        <button type="submit" name="submit">Report Incident</button>
    </form>
</body>

</html>