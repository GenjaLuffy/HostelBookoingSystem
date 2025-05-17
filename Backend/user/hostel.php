<?php
include_once './includes/header.php';
include_once './includes/connect.php';

$sql = "SELECT * FROM hostels ORDER BY id DESC";
$result = $con->query($sql);
?>

    <section class="heros">
        <h1>Hostel</h1>
        <div class="search-boxs">
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
    <div class="hostels">
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($hostel = $result->fetch_assoc()): ?>
                <?php
                $id = (int) $hostel['id'];
                $name = htmlspecialchars($hostel['name']);
                $price = htmlspecialchars($hostel['fee']);
                $location = htmlspecialchars($hostel['location']);
                $type = htmlspecialchars($hostel['type'] ?? 'Not specified');
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