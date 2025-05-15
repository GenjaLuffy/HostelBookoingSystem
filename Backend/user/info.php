<?php
include_once './includes/header.php';
include_once './includes/connect.php';

// Get hostel ID from query string or default to 1
$hostel_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Fetch hostel data
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

<main class="container">

    <section class="top-section">
        <div class="images">
            <img src="../admin/<?php echo htmlspecialchars($hostel['image']); ?>" alt="Room 1">
            <img src="../admin/<?php echo htmlspecialchars($hostel['image2']); ?>" alt="Room 2">
            <img src="../admin/<?php echo htmlspecialchars($hostel['image3']); ?>" alt="Room 3">
            <img src="../admin/<?php echo htmlspecialchars($hostel['image4']); ?>" alt="Room 4">
        </div>

        <h2 id="hostelTitle"><?php echo htmlspecialchars($hostel['name']); ?></h2>
        <!-- You can replace below with dynamic sharing/bed info if stored -->
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
                if (!empty($hostel['amenities'])) {
                    $amenities = explode(',', $hostel['amenities']);
                    foreach ($amenities as $amenity) {
                        $amenity = trim($amenity);
                        if ($amenity) {
                            echo '<li><i class="fa fa-check"></i> ' . htmlspecialchars($amenity) . '</li>';
                        }
                    }
                }
                ?>
            </ul>

            <h3>Hostel Rules</h3>
            <ul class="rules">
                <?php
                if (!empty($hostel['rules'])) {
                    $rules = explode(',', $hostel['rules']);
                    foreach ($rules as $rule) {
                        $rule = trim($rule);
                        if ($rule) {
                            echo '<li><i class="fa fa-exclamation-circle"></i> ' . htmlspecialchars($rule) . '</li>';
                        }
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

            <div class="checkin-out">
                <h4>Check-in</h4>
                <div class="date-container">
                    <input type="date" name="checkin" id="checkin">
                </div>

                <h4>Check-out</h4>
                <div class="date-container">
                    <input type="date" name="checkout" id="checkout">
                </div><br>
            </div>

            <div class="button-wrapper">
                <a href="book.php?hostel_id=<?php echo $hostel['id']; ?>" class="book-btn">Book Now</a>
            </div>

            <div class="contact-box">
                <p>“Check in as a guest, leave as family.”</p>
            </div>
        </section>

    </div>

    <div class="reviews-section">
        <h3>Reviews</h3>
        <div class="reviews" id="reviewList">
            <!-- Reviews will be loaded here dynamically -->
        </div>

        <div class="comment-box">
            <label>Leave a Comment</label>
            <input type="text" id="reviewInput" placeholder="Message..." />
            <button onclick="submitReview()">Submit</button>
        </div>
    </div>

</main>

<script>
    // Reviews handling using localStorage per hostel
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