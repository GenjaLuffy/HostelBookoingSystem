<?php
include './includes/connect.php';
include './includes/header.php';

$hostel_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($hostel_id <= 0) {
    echo "<p>Invalid hostel ID.</p>";
    exit;
}

// Fetch hostel + admin (owner) details
$sql = "SELECT h.*, a.phone AS owner_phone
        FROM hostels h
        JOIN admins a ON h.created_by = a.id
        WHERE h.id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $hostel_id);
$stmt->execute();
$result = $stmt->get_result();
$hostel = $result->fetch_assoc();

if (!$hostel) {
    echo "<p>Hostel not found.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title><?php echo htmlspecialchars($hostel['name']); ?> | Book Mate</title>
    <link rel="stylesheet" href="./assets/css/info.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<body>

<main class="container">

    <section class="top-section">
        <div class="images">
            <img src="../admin/<?php echo htmlspecialchars($hostel['image']); ?>" alt="Room 1">
            <img src="../admin/<?php echo htmlspecialchars($hostel['image2']); ?>" alt="Room 2">
            <img src="../admin/<?php echo htmlspecialchars($hostel['image3']); ?>" alt="Room 3">
            <img src="../admin/<?php echo htmlspecialchars($hostel['image4']); ?>" alt="Room 4">
        </div>

        <h2 id="hostelTitle"><?php echo htmlspecialchars($hostel['name']); ?></h2>
        <p class="room-info">
            <i class="fa fa-users"></i> 3 Sharing &nbsp;
            <i class="fa fa-bed"></i> 1 Full bed &nbsp;
            <i class="fa fa-bed"></i> 2 Twin bed
        </p>

        <p class="description" id="hostelDesc">
            <?php echo nl2br(htmlspecialchars($hostel['description'])); ?><br>
            <strong>Location:</strong> <?php echo htmlspecialchars($hostel['location']); ?>
        </p>
    </section>

    <div class="info-section-wrapper">

        <!-- Left Amenities Side -->
        <section class="left-side">
            <h3>Room Amenities</h3>
            <ul class="amenities">
                <?php
                $iconMap = [
                    'large bed' => 'fa-bed',
                    'bed' => 'fa-bed',
                    'wifi' => 'fa-wifi',
                    'free wifi' => 'fa-wifi',
                    'storage' => 'fa-suitcase',
                    'luggage' => 'fa-suitcase',
                    'bathroom' => 'fa-bath',
                    'attached bathrooms' => 'fa-bath',
                    'dry cleaning' => 'fa-soap',
                    'cleaning' => 'fa-soap',
                    'ac' => 'fa-wind',
                    'cc-tv' => 'fa-video',
                    'air conditioning' => 'fa-wind',
                    'water supply' => 'fa-water',
                    'kitchen' => 'fa-utensils',
                    'shared kitchen' => 'fa-utensils'
                ];

                if (!empty($hostel['amenities'])) {
                    $amenities = explode(',', $hostel['amenities']);
                    foreach ($amenities as $amenity) {
                        $amenity = trim($amenity);
                        if ($amenity) {
                            $key = strtolower($amenity);
                            $iconClass = 'fa-check';
                            foreach ($iconMap as $keyword => $icon) {
                                if (strpos($key, $keyword) !== false) {
                                    $iconClass = $icon;
                                    break;
                                }
                            }
                            echo '<li><i class="fa ' . $iconClass . '"></i> ' . htmlspecialchars($amenity) . '</li>';
                        }
                    }
                }
                ?>
            </ul>

            <h3>Hostel Rules</h3>
            <ul class="rules">
                <?php
                $ruleIconMap = [
                    'guest' => 'fa-user-lock',
                    'outsider' => 'fa-user-lock',
                    'furniture' => 'fa-couch',
                    'damage' => 'fa-couch',
                    'smoking' => 'fa-smoking-ban',
                    'alcohol' => 'fa-wine-bottle',
                    'quiet' => 'fa-volume-mute',
                    'noise' => 'fa-volume-mute',
                    'cleanliness' => 'fa-broom',
                ];

                $arrivalRule = '';
                $departureRule = '';
                $otherRules = [];

                if (!empty($hostel['rules'])) {
                    $rules = explode(',', $hostel['rules']);
                    foreach ($rules as $rule) {
                        $rule = trim($rule);
                        if (!$rule) continue;

                        if (stripos($rule, 'arrival') !== false) {
                            $arrivalRule = $rule;
                        } elseif (stripos($rule, 'departure') !== false) {
                            $departureRule = $rule;
                        } else {
                            $otherRules[] = $rule;
                        }
                    }

                    if ($arrivalRule || $departureRule) {
                        $combined = trim($arrivalRule . ' & ' . $departureRule, ' & ');
                        echo '<li><i class="fa fa-clock"></i> ' . htmlspecialchars($combined) . '</li>';
                    }

                    foreach ($otherRules as $rule) {
                        $iconClass = 'fa-exclamation-circle';
                        foreach ($ruleIconMap as $keyword => $icon) {
                            if (stripos($rule, $keyword) !== false) {
                                $iconClass = $icon;
                                break;
                            }
                        }
                        echo '<li><i class="fa ' . $iconClass . '"></i> ' . htmlspecialchars($rule) . '</li>';
                    }
                }
                ?>
            </ul>

        </section>

        <!-- Right Booking Panel -->
        <section class="right-side">
            <div class="price" id="hostelPrice">
                Rs. <?php echo number_format($hostel['fee'], 0); ?>
                <span>/ 1 month only</span><br>
                <span><?php echo ($hostel['gender'] === 'Male') ? 'Boys Hostel' : 'Girls Hostel'; ?></span>
            </div>

            <div id="map" style="height: 400px; margin-bottom: 20px;"></div>

            <div class="book-contact-row">
                <a href="book.php?hostel_id=<?php echo $hostel['id']; ?>" class="book-btn">Book Now</a>
                <div class="contact-info-box-inline">
                    <i class="fa fa-phone"></i>
                   <?php echo htmlspecialchars($hostel['owner_phone']); ?>
                   
                </div>
            </div>

            <div class="contact-box">
                <p>“Check in as a guest, leave as family.”</p>
            </div>
        </section>
    </div>
</main>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    const hostelLat = <?php echo !empty($hostel['latitude']) ? (float)$hostel['latitude'] : 27.7172; ?>;
    const hostelLng = <?php echo !empty($hostel['longitude']) ? (float)$hostel['longitude'] : 85.3240; ?>;
    const hostelName = <?php echo json_encode($hostel['name']); ?>;
    const hostelFee = <?php echo (int)$hostel['fee']; ?>;

    const map = L.map('map').setView([hostelLat, hostelLng], 15);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    L.marker([hostelLat, hostelLng])
        .addTo(map)
        .bindPopup(`<strong>${hostelName}</strong><br>Rs ${hostelFee}`)
        .openPopup();
</script>

<!-- Footer -->
<footer style="background-color: #0d1b2a; color: #fff; padding: 40px 0; font-size: 14px;">
  <div style="max-width: 1200px; margin: auto; display: flex; flex-wrap: wrap; justify-content: space-between; gap: 20px; padding: 0 20px;">
    <div style="flex: 1; min-width: 200px;">
      <h3 style="color: #fff;">Bootmate</h3>
      <p style="line-height: 1.8;">Your trusted platform for hostel booking.<br>We make finding the right room simple, reliable,<br>and accessible for students.</p>
      <div style="margin-top: 10px;">
        <a href="#"><i class="fab fa-facebook-f" style="color:#fff; margin-right:10px;"></i></a>
        <a href="#"><i class="fab fa-instagram" style="color:#fff; margin-right:10px;"></i></a>
        <a href="#"><i class="fab fa-twitter" style="color:#fff;"></i></a>
      </div>
    </div>
    <div style="flex: 1; min-width: 150px;">
      <h4 style="color: #fff;">Quick Links</h4>
      <ul style="list-style: none; padding: 0;">
        <li><a href="hostel.php" style="color: #fff; text-decoration: none;">Hostel</a></li><br>
        <li><a href="about.php" style="color: #fff; text-decoration: none;">About Us</a></li><br>
      </ul>
    </div>
    <div style="flex: 1; min-width: 200px;">
      <h4 style="color: #fff;">Contact Info</h4>
      <p><i class="fas fa-map-marker-alt"></i> New Baneshwor, Kathmandu</p>
      <p><i class="fas fa-phone-alt"></i> +977 9800000000</p>
      <p><i class="fas fa-envelope"></i> support@bootmate.com</p>
    </div>
  </div>
  <div style="text-align: center; padding-top: 20px; border-top: 1px solid #444;">
    <p style="margin: 0;">&copy; <?= date("Y") ?> Bootmate. All rights reserved.</p>
    <p style="margin: 0;">
      <a href="#" style="color: #aaa; text-decoration: none;">Privacy Policy</a> |
      <a href="#" style="color: #aaa; text-decoration: none;">Terms of Service</a>
    </p>
  </div>
</footer>

</body>
</html>
