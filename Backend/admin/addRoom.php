<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add Room</title>
  <link rel="stylesheet" href="assets/css/addRoom.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
  <header class="header">
    <a href="index.html" class="logo">Book<br><span>Mate</span></a>
  </header>

  <div class="container">
    <aside class="sidebar">
      <nav>
        <a href="index.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
        <a href="profile.php"><i class="fas fa-user"></i><span>Profile</span></a>
        <a href="addRoom.php"><i class="fas fa-door-open"></i><span>Rooms</span></a>
        <a href="#"><i class="fas fa-cogs"></i><span>Managed Rooms</span></a>
        <a href="addHostel.php"><i class="fas fa-bed"></i><span>Add Hostel</span></a>
        <a href="#"><i class="fas fa-users"></i><span>Manage Students</span></a>
      </nav>
    </aside>

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
