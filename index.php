<?php

include 'components/dbconfig.php';


?>

<!DOCTYPE html>
<html>

<head>
    <title>Garbage Collection Map</title>
    <style>
        #map-container {
            display: flex;
            flex-direction: row;
            height: 100vh;
        }

        #incidents-container {
            width: 30%;
            background-color: #f1f1f1;
            padding: 10px;
            box-sizing: border-box;
            overflow-y: auto;
        }

        #map {
            flex: 1;
            height: 100%;
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
                // Create a marker for this incident
                var marker = new google.maps.Marker({
                    position: {
                        lat: <?php echo $latitude; ?>,
                        lng: <?php echo $longitude; ?>
                    },
                    map: map,
                    title: '<?php echo $name; ?>'
                });

                // Create an info window for this incident
                var infowindow = new google.maps.InfoWindow({
                    content: '<div><p><?php echo $name; ?></p><img src="<?php echo 'GTF/' .$image_path; ?>" width="200"></div>'
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
    <div id="map-container">
        <div id="incidents-container">
            <h2 style="font-size:large;">Reported Incidents</h2>
            <ul id="incidents"></ul>
        </div>
        <div id="map"></div>
    </div>

    <script src="js/gtf.js"></script>
</body>

</html>