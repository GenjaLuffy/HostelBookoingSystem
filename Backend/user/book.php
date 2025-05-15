<?php
include_once './includes/header.php';
?>
    <h2>Book Now</h2>
    <div class="banner">Fill All Info</div>

    <form onsubmit="submitForm(event)">
        <div class="form-wrapper">
            <!-- LEFT COLUMN -->
            <div class="form-left">
                <fieldset>
                    <legend>Room Related Info</legend>
                    <div class="flex-row">
                        <div class="form-group">
                            <label>Room no.</label>
                            <select required>
                                <option>Select Room</option>
                                <option>101</option>
                                <option>102</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Seater</label>
                            <input type="text" required>
                        </div>
                        <div class="form-group">
                            <label>Fees Per Month</label>
                            <input type="text" required>
                        </div>
                        <div class="form-group">
                            <label>Food Status</label>
                            <label><input type="radio" name="food" checked> Without Food</label>
                            <label><input type="radio" name="food"> With Food (Rs 2000 Per Month Extra)</label>
                        </div>

                        <div class="form-group">
                            <label>Stay From</label>
                            <input type="date" required>
                        </div>
                        <div class="form-group">
                            <label>Stay Duration</label>
                            <select required>
                                <option>Select Duration in Month</option>
                                <option>3</option>
                                <option>6</option>
                            </select>
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Personal Info</legend>
                    <div class="flex-row">
                        <div class="form-group">
                            <label>First Name</label>
                            <input type="text" required>
                        </div>
                        <div class="form-group">
                            <label>Middle Name</label>
                            <input type="text">
                        </div>
                        <div class="form-group">
                            <label>Last Name</label>
                            <input type="text" required>
                        </div>
                        <div class="form-group">
                            <label>Gender</label>
                            <select required>
                                <option>Select Gender</option>
                                <option>Male</option>
                                <option>Female</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Contact No</label>
                            <input type="text" required>
                        </div>
                        <div class="form-group">
                            <label>Guardian Name</label>
                            <input type="text">
                        </div>
                        <div class="form-group">
                            <label>Guardian Contact No</label>
                            <input type="text">
                        </div>
                        <div class="form-group">
                            <label>Image</label>
                            <input type="file">
                        </div>
                    </div>
                </fieldset>
            </div>

            <!-- RIGHT COLUMN -->
            <div class="form-right">
                <fieldset>
                    <legend>Correspondence Address</legend>
                    <div class="flex-row">
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text">
                        </div>
                        <div class="form-group">
                            <label>City</label>
                            <input type="text">
                        </div>
                        <div class="form-group">
                            <label>District</label>
                            <input type="text">
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Permanent Address</legend>
                    <div class="flex-row">
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text">
                        </div>
                        <div class="form-group">
                            <label>City</label>
                            <input type="text">
                        </div>
                        <div class="form-group">
                            <label>District</label>
                            <input type="text">
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>

        <button type="submit" class="submit-btn">Submit</button>
    </form>

    <script>
        function submitForm(event) {
            event.preventDefault();
            alert('Form submitted successfully!');
        }
    </script>
</body>

</html>