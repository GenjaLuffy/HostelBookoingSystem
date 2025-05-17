<?php
include './includes/header.php';
include './includes/connect.php';
session_start();
include 'includes/auth.php'; 
?>
    <main class="main-content">
      <h1>Add Room</h1>
      <div class="form-card">
        <div class="form-header">Add Room</div>
        <form id="roomForm">
          <label for="roomNo">Room No.</label>
          <input type="text" id="roomNo" required>

          <label for="seater">Select Seater</label>
          <select id="seater" required>
            <option value="">-- Select --</option>
            <option value="1">1 Seater</option>
            <option value="2">2 Seater</option>
            <option value="3">3 Seater</option>
            <option value="4">4 Seater</option>
          </select>

          <label for="fee">Fee (Per Student)</label>
          <input type="number" id="fee" required min="5000">

          <button type="submit">Create Room</button>
        </form>
      </div>
    </main>
  </div>
</body>
</html>
