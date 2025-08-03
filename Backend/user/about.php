<?php
include './includes/header.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>About Us</title>
  <link rel="stylesheet" href="assets/css/style.css">
   <link rel="stylesheet" href="./assets/css/about.css" />
</head>
<body>
  <!-- About Page Section -->
  <section class="about-page">
    <div class="about-container">
      <h1>About Us</h1>
      <p>
        Welcome to <strong>Book Mate</strong>, your trusted companion for finding and booking the perfect hostel stay — anytime, anywhere.
        We created Book Mate with one mission in mind: to make budget-friendly, safe, and convenient accommodations easily accessible for students, backpackers, and everyday travelers.
        At Book Mate, we believe that booking a hostel should be as easy as making a friend. That’s why we’ve built a platform that’s user-friendly, transparent, and full of options that suit every lifestyle and budget.
      </p>
    </div>
  </section>

  <!-- About Us Section with Image and Paragraph -->
  <section class="about-highlight">
    <div class="about-split">
      <div class="about-split-image">
        <img src="./assets/images/s5.jpg" alt="Hostel Search Nepal" />
      </div>
      <div class="about-split-text">
        <p>
          <strong>A True Hostel Finder in Nepal</strong> is a convenient solution for those seeking accommodation near their college, schools, or workplace.
          Book Mate offers an easy and hassle-free way to discover hostels in Kathmandu, Lalitpur, Bhaktapur, and many more in Nepal.
        </p>
      </div>
    </div>
  </section>

  <!-- What We Offer Section -->
  <section class="what-we-offer">
  <h3>What We Offer!!!</h3>
  <ul>
    <li><i class="fas fa-arrow-right"></i> Verified hostel listings with real photos and reviews</li>
    <li><i class="fas fa-arrow-right"></i> Instant booking and confirmation</li>
    <li><i class="fas fa-arrow-right"></i> Easy filters by location, price, amenities, and more</li>
    <li><i class="fas fa-arrow-right"></i> Safe and secure payment options</li>
    <li><i class="fas fa-arrow-right"></i> 24/7 support for peace of mind</li>
  </ul>
</section>


<!-- Footer -->
<footer style="background-color: #0d1b2a; color: #fff; padding: 40px 0; font-size: 14px;">
  <div style="max-width: 1200px; margin: auto; display: flex; flex-wrap: wrap; justify-content: space-between; gap: 20px; padding: 0 20px;">

    <!-- Logo and Description -->
    <div style="flex: 1; min-width: 200px;">
      <h3 style="color: #fff;">Bootmate</h3>
      <p style="line-height: 1.8;">
        Your trusted platform for hostel booking. <br>
        We make finding the right room simple, reliable, <br>
        and accessible for students.
      </p>
      <div style="margin-top: 10px;">
        <a href="#"><i class="fab fa-facebook-f" style="color:#fff; margin-right:10px;"></i></a>
        <a href="#"><i class="fab fa-instagram" style="color:#fff; margin-right:10px;"></i></a>
        <a href="#"><i class="fab fa-twitter" style="color:#fff;"></i></a>
      </div>
    </div>

    <!-- Quick Links -->
    <div style="flex: 1; min-width: 150px;">
      <h4 style="color: #fff;">Quick Links</h4>
      <ul style="list-style: none; padding: 0;">
        <li><a href="hostel.php" style="color: #fff; text-decoration: none;">Hostel</a></li><br>
        <li><a href="about.php" style="color: #fff; text-decoration: none;">About Us</a></li><br>
      </ul>
    </div>

    <!-- Contact Info -->
    <div style="flex: 1; min-width: 200px;">
      <h4 style="color: #fff;">Contact Info</h4>
      <p><i class="fas fa-map-marker-alt"></i> New Baneshwor, Kathmandu</p>
      <p><i class="fas fa-phone-alt"></i> +977 9800000000</p>
      <p><i class="fas fa-envelope"></i> support@bootmate.com</p>
    </div>
  </div>

  <!-- Bottom -->
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
