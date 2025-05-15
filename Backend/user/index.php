<?php
include_once './includes/header.php';
include_once './includes/connect.php';

$sql = "SELECT * FROM hostels ORDER BY id DESC";
$result = $con->query($sql);
?>

<section class="hero">
  <div class="hero-text">
    <h1>Welcome To <br>Book Mate</h1>
    <p>"Find Your Stay, Fuel Your Adventure"</p>
    <button>Book Now</button>
  </div>
  <div class="search-box">
    <div class="location-input">
      <input type="text" placeholder="Search by Near Me, City" />
      <i class="fas fa-map-marker-alt"></i>
    </div>
    <select>
      <option>Price: Rs 5000</option>
      <option>Price: Rs 10000</option>
      <option>Price: Rs 15000</option>
    </select>
    <select>
      <option>Gender</option>
      <option>Male</option>
      <option>Female</option>
      <option>Other</option>
    </select>
    <button class="search-btn">Search <i class="fas fa-search"></i> </button>
  </div>
</section>

<section class="featured">
  <h2>Featured Hostels</h2>
  <div class="hostels">
    <?php
    if ($result && $result->num_rows > 0) {
      while ($hostel = $result->fetch_assoc()) {
        // Fix image path relative to current file location
        $image1 = !empty($hostel['image']) ? "../admin/" . $hostel['image'] : 'assets/images/default.jpg';

        $id = (int)$hostel['id'];
        $name = htmlspecialchars($hostel['name']);
        $price = htmlspecialchars($hostel['fee']);
        $location = htmlspecialchars($hostel['location']);
        $type = htmlspecialchars($hostel['type'] ?? 'Not specified');  
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