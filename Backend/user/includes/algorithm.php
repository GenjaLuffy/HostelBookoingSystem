<?php

// Geocode location using OpenStreetMap Nominatim API with cURL
function geocodeLocation($location) {
    $location_enc = urlencode($location);
    $url = "https://nominatim.openstreetmap.org/search?q={$location_enc}&format=json&limit=1";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'User-Agent: BookMateApp/1.0 (contact@yourdomain.com)'
    ]);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode !== 200 || !$response) {
        return null;
    }

    $data = json_decode($response, true);
    if (empty($data)) return null;

    return [
        'lat' => floatval($data[0]['lat']),
        'lng' => floatval($data[0]['lon']),
    ];
}


// Get filtered hostels from DB based on location, price, gender
function getFilteredHostels($con) {
    $location = isset($_GET['location']) ? trim($_GET['location']) : '';
    $price = isset($_GET['price']) ? trim($_GET['price']) : '';
    $gender = isset($_GET['gender']) ? trim($_GET['gender']) : '';

    $conditions = [];
    $conditions[] = "status = 'Approved'";

    if (!empty($price) && is_numeric($price)) {
        $price_val = (float)$price;
        $conditions[] = "fee <= $price_val";
    }

    if (!empty($gender) && in_array($gender, ['Boys Hostel', 'Girls Hostel', 'Other'])) {
        $gen_safe = $con->real_escape_string($gender);
        $conditions[] = "gender = '$gen_safe'";
    }

    $whereClause = count($conditions) > 0 ? "WHERE " . implode(' AND ', $conditions) : "";

    $radius = 2; // radius in km
    $lat = null;
    $lng = null;
    $nearbyResult = null;

    if (!empty($location)) {
        $coords = geocodeLocation($location);
        if ($coords) {
            $lat = $coords['lat'];
            $lng = $coords['lng'];

            $sql = "
                SELECT *, (
                    6371 * acos(
                        cos(radians(?)) * cos(radians(latitude)) *
                        cos(radians(longitude) - radians(?)) +
                        sin(radians(?)) * sin(radians(latitude))
                    )
                ) AS distance
                FROM hostels
                $whereClause
                HAVING distance <= ?
                ORDER BY distance ASC, id DESC
                LIMIT 20
            ";

            $stmt = $con->prepare($sql);
            if ($stmt) {
                $stmt->bind_param('dddi', $lat, $lng, $lat, $radius);
                $stmt->execute();
                $nearbyResult = $stmt->get_result();
                $stmt->close();
            }
        }
    }

    if ($nearbyResult && $nearbyResult->num_rows > 0) {
        return $nearbyResult;
    } else {
        // fallback to broader search if no nearby hostels found
        $fallbackConditions = $conditions;

        if (!empty($location)) {
            $loc_safe = $con->real_escape_string($location);
            $fallbackConditions[] = "location LIKE '%$loc_safe%'";
        }

        $fallbackWhereClause = count($fallbackConditions) > 0 ? "WHERE " . implode(' AND ', $fallbackConditions) : "";

        $fallbackSql = "SELECT * FROM hostels $fallbackWhereClause ORDER BY id DESC LIMIT 50";

        return $con->query($fallbackSql);
    }
}
