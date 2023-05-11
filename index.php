<?php

include 'components/dbconfig.php';

session_start();

$captain_id = $_SESSION['captain_id'];

if (!isset($captain_id)) {
    header('location:captain_login.php');
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Garbage Collection Map</title>
    <style>
        #map-container {
            display: flex;
            flex-direction: row;
            height: 90vh;
        }

        #incidents-container {
            width: 30%;
            background-color: #f1f1f1;
            padding: 10px;
            box-sizing: border-box;
            height: calc(100vh - 70px);
            overflow-y: auto;
        }

        #map {
            width: 70%;
            height: 100%;
            /* update this property */
        }

        #incidents {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        #incidents li {
            font-size: large;
            padding: 10px;
            border-bottom: 1px solid #ccc;
            cursor: pointer;
        }

        #incidents li:hover {
            background-color: #ddd;
        }

        /* The Modal (background) */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1;
            /* Sit on top */
            padding-top: 100px;
            /* Location of the box */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgb(0, 0, 0);
            /* Fallback color */
            background-color: rgba(0, 0, 0, 0.4);
            /* Black w/ opacity */
        }

        /* Modal Content */
        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
        }

        /* The Close Button */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/gtf.css">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCu4iq-uhkrhk3_KHaCWRpBvsj-yZjBZfc"></script>
    <script>
        function initMap() {
            // Create a map instance with initial center and zoom level
            var map = new google.maps.Map(document.getElementById('map'), {
                center: {
                    lat: 6.9271,
                    lng: 79.8612
                },
                zoom: 10
            });

            // Create an array to hold all the markers and info windows
            var markers = [];

            // Fetch incidents data from the database
            <?php
            $db = mysqli_connect('localhost', 'root', '', 'garbage_collection');
            $query = "SELECT * FROM incidents";
            $result = mysqli_query($db, $query);
            while ($row = mysqli_fetch_assoc($result)) {
                $latitude = $row['latitude'];
                $longitude = $row['longitude'];
                $name = $row['name'];
                $description = $row['description'];
                $image_path = $row['image_path'];
            ?>
                // craete a marker for this incident
                var marker = new google.maps.Marker({
                    position: {
                        lat: <?php echo $latitude; ?>,
                        lng: <?php echo $longitude; ?>
                    },
                    map: map,
                    title: '<?php echo $name; ?>'
                });

                //info window for marker
                var infowindow = new google.maps.InfoWindow({
                    content: '<div><p><?php echo $name; ?></p><img src="<?php echo 'GTF/' . $image_path; ?>" width="200"></div>'
                });


                // Add a click event listener to the marker
                marker.addListener('click', (function(marker, infowindow) {
                    return function() {
                        // Close any other open info windows
                        markers.forEach(function(m) {
                            m.infowindow.close();
                        });
                        // Open the info window for this marker
                        infowindow.open(map, marker);
                    };
                })(marker, infowindow));

                // Add a marker and  info window to the array
                markers.push({
                    marker: marker,
                    infowindow: infowindow
                });

                // Add a new <li> element for each incident to the #incidents <ul> element
                var li = document.createElement('li');
                li.innerHTML = '<?php echo $name; ?>';
                li.addEventListener('click', (function(marker, infowindow) {
                    return function() {
                        // set map center to marker location
                        map.setCenter({
                            lat: <?php echo $latitude; ?>,
                            lng: <?php echo $longitude; ?>
                        });
                        // set the zoom level to a value other than the default
                        map.setZoom(15);
                        // close any other open info windows
                        markers.forEach(function(m) {
                            m.infowindow.close();
                        });
                        // open the info window for this marker
                        infowindow.open(map, marker);
                    };
                })(marker, infowindow));
                document.getElementById('incidents').appendChild(li);
            <?php
            }
            mysqli_close($db);
            ?>
        }
    </script>
</head>

<body onload="initMap()">
    <?php include 'components/captain_header.php'; ?>
    <div id="map-container">
        <div id="incidents-container">
            <h2 style="font-size:large;">Reported Incidents</h2>
            <ul id="incidents"></ul>
        </div>
        <div id="map"></div>
        <!-- Modal -->
        <div id="detailsModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h1 id="modalName"></h1>
                <p id="modalLat"></p>
                <p id="modalLng"></p>
                <p id="modalDesc"></p>
                <img id="modalImg" src="" width="200">
            </div>
        </div>

    </div>

    <script src="js/gtf.js"></script>
</body>

</html>