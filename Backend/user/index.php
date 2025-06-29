<?php
include_once './includes/header.php';
include_once './includes/connect.php';
include_once './includes/algorithm.php';

$result = getFilteredHostels($con);

// Retain search values in form
$location = isset($_GET['location']) ? $_GET['location'] : '';
$price = isset($_GET['price']) ? $_GET['price'] : '';
$gender = isset($_GET['gender']) ? $_GET['gender'] : '';
?>

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
      <input type="text" name="location" placeholder="Search by Near Me, City" value="<?php echo htmlspecialchars($location); ?>" />
      <!-- <i class="fas fa-map-marker-alt"></i> -->
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
      <option value="Boys Hostel" <?php if ($gender == 'Boys Hostel') echo 'selected'; ?>>Boys Hostel</option>
      <option value="Girls Hostel" <?php if ($gender == 'Girls Hostel') echo 'selected'; ?>>Girls Hostel</option>
      <option value="Other" <?php if ($gender == 'Other') echo 'selected'; ?>>Other</option>
    </select>
    <button type="submit" class="search-btn">Search <i class="fas fa-search"></i></button>
  </form>
</section>

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
          onclick="window.location.href='info.php?id=<?php echo $id; ?>'"
          style="cursor:pointer;"
          data-name="<?php echo $name; ?>"
          data-price="Rs <?php echo $price; ?>"
          data-location="<?php echo $location; ?>"
          data-image="<?php echo $image1; ?>"
          data-type="<?php echo $type; ?>"
          data-desc="<?php echo $desc; ?>">

          <img src="<?php echo $image1; ?>" alt="<?php echo $name; ?>" />
          <h3><?php echo $name; ?></h3>
          <p>Rs <?php echo $price; ?> ★★★★☆<br><?php echo $location; ?></p>
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

<section class="about">
  <div class="about-content">
    <div class="about-image">
      <img src="./assets/images/2.jpg" alt="About Us Image" />
    </div>
    <div class="about-text">
      <h3>About Us</h3>
      <p>
        A True Hostel Finder in Nepal is a convenient solution for those seeking accommodation near their college,
        schools, or workplace. Book Mate offers an easy and hassle-free way to discover hostels in Kathmandu,
        Lalitpur, Bhaktapur, and many more in Nepal...
      </p>
    </div>
  </div>
</section>

</body>
</html>
