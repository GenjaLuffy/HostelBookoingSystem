<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Booking History</title>
  <style>
   body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
}

header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #f2e6ff;
    padding: 10px 30px;
}

.logo {
    font-size: 22px;
    max-height: fit-content;
    font-weight: bold;
    color: #8667F2;
    text-decoration: none;
    /* color: inherit; */
    font-family: Irish Grover;
    cursor: pointer; /* Makes it clear it's interactive */
    transition: color 0.3s ease, transform 0.3s ease;
}

.logo span {
    color: #c56cf0;
    transition: color 0.3s ease;
}

.logo:hover {
    color: #654ac6; /* Slightly darker or playful color */
    transform: scale(1.05); /* Slight grow effect */
}

.logo:hover span {
    color: #a05ce0;
}
  /* Main content */
    .main-content {
      margin-left: 80px;
      padding: 30px;
    }

    h1 {
    font-size: 24px;
    margin-bottom: 20px;
    font-weight: bold;
    color: #3f3e3e;
}

    .card {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 5px rgba(0,0,0,0.1);
      max-width: 900px;
      
    }

    .card h3 {
      margin-bottom: 20px;
      color: #626262;
    }

    table {
      width: 100%;
      border-collapse: collapse;
    }

    th, td {
      padding: 12px 15px;
      border: 1px solid #ddd;
      text-align: center;
    }

    th {
      background-color: #dee2e6;
      color: #333;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .status-approved {
      background-color: #28a745;
      color: white;
      padding: 5px 10px;
      border-radius: 15px;
      font-size: 14px;
      display: inline-block;
      cursor: pointer;
    }

    .status-cancelled {
      background-color: #dc3545;
      color: white;
      padding: 5px 10px;
      border-radius: 15px;
      font-size: 14px;
      display: inline-block;
      cursor: pointer;
    }

  
  </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
</head>
<body>
    <header class="header">
        <a href="index.html" class="logo">Book<br><span>Mate</span></a>
    </header>

  <div class="main-content">
    <h1>Booking History</h1>
    <div class="card">
      <h3>All Booking Records</h3>
      <table>
        <thead>
          <tr>
            <th>Sno.</th>
            <th>Hostel</th>
            <th>Room No</th>
            <th>Seater</th>
            <th>Stay From</th>
            <th>Duration</th>
            <th>Status</th>
            <th>Total Fee</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>Rose Hostel</td>
            <td>101</td>
            <td>2</td>
            <td>2024-05-01</td>
            <td>6 Months</td>
            <td><span class="status-approved">Approved</span></td>
            <td>Rs. 24000</td>
          </tr>
          <tr>
            <td>2</td>
            <td>Lily Hostel</td>
            <td>203</td>
            <td>1</td>
            <td>2023-10-15</td>
            <td>3 Months</td>
            <td><span class="status-cancelled">Cancelled</span></td>
            <td>Rs. 15000</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

</body>
</html>
