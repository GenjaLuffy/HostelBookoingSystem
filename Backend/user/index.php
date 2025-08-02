<?php
include_once './includes/header.php';
include_once './includes/connect.php';
include_once './includes/algorithm.php';

$result = getFilteredHostels($con);

// Retain search values in form
$location = $_GET['location'] ?? '';
$price = $_GET['price'] ?? '';
$gender = $_GET['gender'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Book Mate</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<!-- Hero Section -->
<section class="hero">
  <div class="hero-text">
    <h1>Welcome To <br>Book Mate</h1>
    <p>"Find Your Stay, Fuel Your Adventure"</p>
    <a href="hostel.php">
      <button>See Hostel</button>
    </a>
  </div>

  <form method="GET" action="" class="search-box">
    <div class="location-input">
      <input type="text" name="location" placeholder="Search by Near Me, City" value="<?= htmlspecialchars($location); ?>" />
    </div>

    <select name="price">
      <option value="">Select Price</option>
      <?php
      $prices = [1000, 1500, 2000, 2500, 3000, 5000, 6000, 7000];
      foreach ($prices as $p) {
        $selected = ($price == $p) ? 'selected' : '';
        echo "<option value='$p' $selected>Price: Rs $p</option>";
      }
      ?>
    </select>

    <select name="gender">
      <option value="">Gender</option>
      <option value="Boys Hostel" <?= $gender == 'Boys Hostel' ? 'selected' : ''; ?>>Boys Hostel</option>
      <option value="Girls Hostel" <?= $gender == 'Girls Hostel' ? 'selected' : ''; ?>>Girls Hostel</option>
      <option value="Other" <?= $gender == 'Other' ? 'selected' : ''; ?>>Other</option>
    </select>

    <button type="submit" class="search-btn">Search <i class="fas fa-search"></i></button>
  </form>
</section>

<!-- Featured Hostels -->
<section class="featured">
  <h2>Featured Hostels</h2>
  <div class="hostels">
    <?php
    if ($result && $result->num_rows > 0) {
      $count = 0;
      while ($hostel = $result->fetch_assoc()) {
        if ($count >= 4) break;

        $image1 = !empty($hostel['image']) ? "../admin/" . $hostel['image'] : 'assets/images/default.jpg';
        $id = (int)$hostel['id'];
        $name = htmlspecialchars($hostel['name']);
        $price = htmlspecialchars($hostel['fee']);
        $location = htmlspecialchars($hostel['location']);
        $type = htmlspecialchars($hostel['gender'] ?? 'Not specified');
        $desc = htmlspecialchars($hostel['description'] ?? '');
    ?>
        <div class="hostel-card"
          onclick="window.location.href='info.php?id=<?= $id; ?>'"
          style="cursor:pointer;"
          data-name="<?= $name; ?>"
          data-price="Rs <?= $price; ?>"
          data-location="<?= $location; ?>"
          data-image="<?= $image1; ?>"
          data-type="<?= $type; ?>"
          data-desc="<?= $desc; ?>">

          <img src="<?= $image1; ?>" alt="<?= $name; ?>" />
          <h3><?= $name; ?></h3>
          <p>Rs <?= $price; ?><br><?= $location; ?></p>
        </div>
    <?php
        $count++;
      }
    } else {
      echo "<p>No hostels found.</p>";
    }
    ?>
  </div>
</section>

<!-- About Section -->
<section id="about" class="about">
  <div class="about-content">
    <div class="about-image">
      <img src="./assets/images/2.jpg" alt="About Us Image" />
    </div>
    <div class="about-text">
      <a href="about.php"><h3>About Us</h3></a>
      <p>
        A True Hostel Finder in Nepal is a convenient solution for those seeking accommodation near their college,
        schools, or workplace. Book Mate offers an easy and hassle-free way to discover hostels in Kathmandu,
        Lalitpur, Bhaktapur, and many more in Nepal...
      </p>
    </div>
  </div>
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
        <li><a href="book.php" style="color: #fff; text-decoration: none;">Book a Room</a></li><br>
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
