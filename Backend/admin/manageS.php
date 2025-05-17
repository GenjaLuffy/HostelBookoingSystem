<?php
include './includes/header.php';
include './includes/connect.php';
session_start();
include 'includes/auth.php'; 

$user_id = $_SESSION['user_id'];  // Assuming you store logged-in user's ID here

?>

<main class="main-content">
    <h1>Manage Student</h1>
    <div class="table-container">
        <h2>All Student Details</h2>
        <table>
            <thead>
                <tr>
                    <th>Sno.</th>
                    <th>Student Name</th>
                    <th>Contact no.</th>
                    <th>Room no.</th>
                    <th>Seater</th>
                    <th>Staying From</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Prepare statement to fetch bookings for logged-in user
                $stmt = $con->prepare("SELECT id, first_name, middle_name, last_name, contact_no, room_no, seater, stay_from FROM bookings WHERE user_id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                $sno = 1;
                while ($row = $result->fetch_assoc()) {
                    $full_name = htmlspecialchars($row['first_name'] . ' ' . $row['middle_name'] . ' ' . $row['last_name']);
                    $contact_no = htmlspecialchars($row['contact_no']);
                    $room_no = htmlspecialchars($row['room_no']);
                    $seater = htmlspecialchars($row['seater']);
                    $stay_from = htmlspecialchars($row['stay_from']);
                    $booking_id = $row['id'];

                    echo "<tr>
                            <td>{$sno}</td>
                            <td>{$full_name}</td>
                            <td>{$contact_no}</td>
                            <td>{$room_no}</td>
                            <td>{$seater}</td>
                            <td>{$stay_from}</td>
                            <td>
                                <a href='edit_booking.php?id={$booking_id}'><i class='fas fa-edit'></i></a>
                                <a href='delete_booking.php?id={$booking_id}' onclick=\"return confirm('Are you sure you want to delete this booking?');\"><i class='fas fa-times'></i></a>
                            </td>
                          </tr>";
                    $sno++;
                }

                $stmt->close();
                ?>
            </tbody>
        </table>
    </div>
</main>
</body>
</html>
