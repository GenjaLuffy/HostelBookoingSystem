<?php
include_once './includes/header.php';
include_once './includes/connect.php';
include_once './includes/algorithm.php';

// Get the filtered result from algorithm
$result = getFilteredHostels($con);

// Retain selected filter values in form
$location = isset($_GET['location']) ? $_GET['location'] : '';
$price = isset($_GET['price']) ? $_GET['price'] : '';
$gender = isset($_GET['gender']) ? $_GET['gender'] : '';
?>

<section class="heros">
    <h1>Hostel</h1>
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
    <div class="hostels">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($hostel = $result->fetch_assoc()): ?>
                <?php
                $id = (int)$hostel['id'];
                $name = htmlspecialchars($hostel['name']);
                $price = htmlspecialchars($hostel['fee']);
                $location = htmlspecialchars($hostel['location']);
                $type = htmlspecialchars($hostel['gender'] ?? 'Not specified');
                $desc = htmlspecialchars($hostel['description'] ?? '');
                $image = !empty($hostel['image']) ? "../admin/" . $hostel['image'] : "./assets/images/default.jpg";
                ?>
                <div class="hostel-card"
                     onclick="window.location.href='info.php?id=<?php echo $id; ?>'"
                     style="cursor:pointer;"
                     data-name="<?php echo $name; ?>"
                     data-price="Rs <?php echo $price; ?>"
                     data-location="<?php echo $location; ?>"
                     data-image="<?php echo $image; ?>"
                     data-type="<?php echo $type; ?>"
                     data-desc="<?php echo $desc; ?>">
                    <img src="<?php echo $image; ?>" alt="<?php echo $name; ?>" />
                    <h3><?php echo $name; ?></h3>
                    <p>Rs <?php echo $price; ?> ★★★★☆<br><?php echo $location; ?></p>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No hostels found.</p>
        <?php endif; ?>
    </div>
</section>

</body>
</html>
