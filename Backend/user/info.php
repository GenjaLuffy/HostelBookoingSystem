<?php
include_once './includes/header.php';
include_once './includes/connect.php';

$hostel_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

$sql = "SELECT * FROM hostels WHERE id = ?";
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
                    'air conditioning' => 'fa-wind',
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
                    'cleanliness' => 'fa-broom'
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
                    9879679878
                </div>
            </div>

            <div class="contact-box">
                <p>“Check in as a guest, leave as family.”</p>
            </div>
        </section>
    </div>

    <div class="reviews-section">
        <h3>Reviews</h3>
        <div class="reviews" id="reviewList"></div>

        <div class="comment-box">
            <label>Leave a Comment</label>
            <input type="text" id="reviewInput" placeholder="Message..." />
            <button onclick="submitReview()">Submit</button>
        </div>
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

<script>
    const hostelId = <?php echo json_encode($hostel['id']); ?>;
    const reviewKey = `reviews_${hostelId}`;
    const reviewList = document.getElementById('reviewList');

    function loadReviews() {
        reviewList.innerHTML = '';
        const reviews = JSON.parse(localStorage.getItem(reviewKey)) || [];
        reviews.forEach((r, index) => {
            const div = document.createElement('div');
            div.className = 'review-card';
            div.innerHTML = `
                👤 <strong>${r.name}</strong> &nbsp; ${r.stars}<br>
                <span>${r.message}</span>
                <button onclick="deleteReview(${index})" style="margin-left:10px; color:red; border:none; background:none; cursor:pointer;">🗑️ Delete</button>
            `;
            reviewList.appendChild(div);
        });
    }

    function submitReview() {
        const message = document.getElementById('reviewInput').value.trim();
        const name = prompt("Enter your name:");
        const stars = parseInt(prompt("Rate out of 5 (e.g., 4):"));
        if (message && name && stars >= 1 && stars <= 5) {
            const newReview = {
                name: name,
                stars: "★".repeat(stars) + "☆".repeat(5 - stars),
                message: message
            };
            const reviews = JSON.parse(localStorage.getItem(reviewKey)) || [];
            reviews.push(newReview);
            localStorage.setItem(reviewKey, JSON.stringify(reviews));
            document.getElementById("reviewInput").value = "";
            loadReviews();
        } else {
            alert("Please enter valid name, rating (1-5), and message.");
        }
    }

    function deleteReview(index) {
        const reviews = JSON.parse(localStorage.getItem(reviewKey)) || [];
        if (index >= 0 && index < reviews.length) {
            if (confirm("Are you sure you want to delete this review?")) {
                reviews.splice(index, 1);
                localStorage.setItem(reviewKey, JSON.stringify(reviews));
                loadReviews();
            }
        }
    }

    loadReviews();
</script>

</body>
</html>
