<?php
include_once './includes/header.php';
include_once './includes/connect.php';

// Get search filters from GET parameters
$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$price = isset($_GET['price']) ? trim($_GET['price']) : '';
$gender = isset($_GET['gender']) ? trim($_GET['gender']) : '';

$conditions = [];
$conditions[] = "status = 'Approved'"; // Only approved hostels

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

$whereClause = '';
if (count($conditions) > 0) {
    $whereClause = "WHERE " . implode(' AND ', $conditions);
}

$sql = "SELECT * FROM hostels $whereClause ORDER BY id DESC";
$result = $con->query($sql);
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
      <i class="fas fa-map-marker-alt"></i>
    </div>
    <select name="price">
      <option value="">Select Price</option>
      <option value="1000" <?php if($price == '1000') echo 'selected'; ?>>Price: Rs 1000</option>
      <option value="1500" <?php if($price == '1500') echo 'selected'; ?>>Price: Rs 1500</option>
      <option value="2000" <?php if($price == '2000') echo 'selected'; ?>>Price: Rs 2000</option>
      <option value="2500" <?php if($price == '2500') echo 'selected'; ?>>Price: Rs 2500</option>
      <option value="3000" <?php if($price == '3000') echo 'selected'; ?>>Price: Rs 3000</option>
      <option value="5000" <?php if($price == '5000') echo 'selected'; ?>>Price: Rs 5000</option>
      <option value="6000" <?php if($price == '6000') echo 'selected'; ?>>Price: Rs 6000</option>
      <option value="7000" <?php if($price == '7000') echo 'selected'; ?>>Price: Rs 7000</option>
    </select>
    <select name="gender">
      <option value="">Gender</option>
      <option value="Boys Hostel" <?php if($gender == 'Boys Hostel') echo 'selected'; ?>>Boys Hostel</option>
      <option value="Girls Hostel" <?php if($gender == 'Girls Hostel') echo 'selected'; ?>>Girls Hostel</option>
      <option value="Other" <?php if($gender == 'Other') echo 'selected'; ?>>Other</option>
    </select>
    <button type="submit" class="search-btn">Search <i class="fas fa-search"></i></button>
  </form>
</section>

<section class="featured">
  <h2>Featured Hostels</h2>
  <div class="hostels">
    <?php
    if ($result && $result->num_rows > 0) {
      $count = 0; // limit to 4 hostels
      while ($hostel = $result->fetch_assoc()) {
        if ($count >= 4) break;

        $image1 = !empty($hostel['image']) ? "../admin/" . $hostel['image'] : 'assets/images/default.jpg';
        $id = (int)$hostel['id'];
        $name = htmlspecialchars($hostel['name']);
        $price = htmlspecialchars($hostel['fee']);
        $location = htmlspecialchars($hostel['location']);
        $type = htmlspecialchars($hostel['gender'] ?? 'Not specified');  // Using gender as type here
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
        Lalitpur,
        Bhaktapur, and many more in Nepal...
      </p>
    </div>
  </div>
</section>

</body>
</html>
