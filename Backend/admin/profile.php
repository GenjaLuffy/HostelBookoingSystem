<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Profile</title>
    <link rel="stylesheet" href="assets/css/profile.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

</head>

<body>
    <div class="header">
        <a class="logo" href="#">Book<span>Mate</span></a>
    </div>

    <div class="container">
        <div class="sidebar">
            <nav>
                <a href="index.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
                <a href="profile.php"><i class="fas fa-user"></i><span>Profile</span></a>
                <a href="addRoom.php"><i class="fas fa-door-open"></i><span>Rooms</span></a>
                <a href="#"><i class="fas fa-cogs"></i><span>Managed Rooms</span></a>
                <a href="addHostel.php"><i class="fas fa-bed"></i><span>Add Hostel</span></a>
                <a href="#"><i class="fas fa-users"></i><span>Manage Students</span></a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="edit-profile-container">
                <div class="profile-top">
                    <div class="profile-pic">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="profile-details">
                        <h3>Users Name</h3>
                        <p>Address</p>
                        <p>Student</p>
                    </div>
                </div>

                <form class="edit-form">
                    <div>
                        <label for="fullName">Full Name</label>
                        <input type="text" id="fullName" placeholder="Enter full name">
                    </div>
                    <div>
                        <label for="userName">User Name</label>
                        <input type="text" id="userName" placeholder="Enter user name">
                    </div>
                    <div>
                        <label for="dob">Date of Birth</label>
                        <input type="date" id="dob">
                    </div>
                    <div>
                        <label for="phone">Phone</label>
                        <input type="text" id="phone" placeholder="Enter phone number">
                    </div>
                    <div>
                        <label for="address">Address</label>
                        <input type="text" id="address" placeholder="Enter address">
                    </div>
                    <div>
                        <label for="gender">Gender</label>
                        <input type="text" id="gender" placeholder="Enter gender">
                    </div>
                    <div class="form-buttons">
                        <button type="submit">Save Change</button>
                        <button type="button" class="cancel-btn"
                            onclick="window.location.href='profile.html'">Cancel</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</body>

</html>