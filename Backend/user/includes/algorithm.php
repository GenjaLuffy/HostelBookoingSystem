<?php
function getFilteredHostels($con) {
    // Step 1: Get filters from $_GET
    $location = isset($_GET['location']) ? trim($_GET['location']) : '';
    $price = isset($_GET['price']) ? trim($_GET['price']) : '';
    $gender = isset($_GET['gender']) ? trim($_GET['gender']) : '';

    // Step 2: Build WHERE conditions
    $conditions = [];
    $conditions[] = "status = 'Approved'"; // Always filter approved hostels

    if (!empty($location)) {
        $loc_safe = $con->real_escape_string($location);
        $conditions[] = "location LIKE '%$loc_safe%'";
    }

    if (!empty($price) && is_numeric($price)) {
        $price_val = (float)$price;
        $conditions[] = "fee <= $price_val";
    }

    if (!empty($gender) && in_array($gender, ['Boys Hostel', 'Girls Hostel', 'Other'])) {
        $gen_safe = $con->real_escape_string($gender);
        $conditions[] = "gender = '$gen_safe'";
    }

    // Step 3: Final query
    $whereClause = count($conditions) > 0 ? "WHERE " . implode(' AND ', $conditions) : "";
    $sql = "SELECT * FROM hostels $whereClause ORDER BY id DESC";

    return $con->query($sql);
}
