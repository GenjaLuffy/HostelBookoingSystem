<?php
include './includes/header.php';
include './includes/connect.php';
session_start();
include 'includes/auth.php'; 
?>


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