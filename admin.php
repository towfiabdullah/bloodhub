<!DOCTYPE html>
<html lang="en">
<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>Document</title>
 <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Anton&family=Poetsen+One&display=swap" rel="stylesheet">

   <style>
         /* universal css */
*{
   margin:0px;
   padding:0px;
}
  .navbar_start {
   color: #d91e36;
    text-decoration: none;
    margin-left:50px;
  
  }

  .anton-regular {
  font-family: "Anton", sans-serif;
  font-weight:400;
  font-style
 normal;
}
.body{
 background-color: #f9f9f9;
}
.navbar{
 display:flex;
 justify-content:space-around;
 align-items: center;
 background-color:#f8edeb;
 height:60px;
 margin: 30px 60px;
 border-radius: 20px;
}
.navbar_menu{
   display: flex;
   gap:5px;
     list-style: none;
     margin-left: 100px;

 
}
.container{
  width:100%;
}
.navbar-item{
    margin-left:40px;

}
.navbar-link{
    color:#011627;
    text-decoration:none;

}
.navbar-link:hover{
      color:#d91e36;
}
/* univeral css*/
/* private css */
.summary { display: flex; gap:50px; margin-left:400px; margin-bottom: 20px; }
        .summary-item { background-color: #4CAF50; color: white; padding: 20px; border-radius: 5px; text-align: center; }
        .summary-item h3 { margin: 0; font-size: 2em; }
             
        .admin-pannel
        {
             margin-left:600px;
             margin-top: 50px;
             margin-bottom:50px;

        }

        .donor-summary { margin-bottom: 20px; width:}
        .donor-summary h3 { text-align: center; margin-bottom: 10px; }
        .donor-summary-table { width:100%; border-collapse: collapse; }
        .donor-summary-table th, .donor-summary-table td { border: 1px solid #ddd; padding: 8px; text-align: center; }
        .donor-summary-table th { background-color: #f2f2f2; }

        .card { border: 1px solid #ddd; border-radius: 5px; padding: 15px; margin-bottom: 20px; }
        .card h3 { margin: 0; font-size: 1.5em; }
        .card p { margin: 5px 0; }
   </style>
      
</head>
<body>

<div class="container">
 <!-- private html -->
  <div class="navbar">
   <a href="#" class="navbar_start anton-regular ">BloodHub </a>
   <ul class="navbar_menu">
    <li class="navbar-item"> <a href="index.php " class="navbar-link">Home</a></li>
    <li class="navbar-item"> <a href="register.php " class="navbar-link">Register</a></li>
    <li class="navbar-item"> <a href="request.php " class="navbar-link">Request</a></li>
    <li class="navbar-item"> <a href="search.php " class="navbar-link">Search </a></li>
    <li class="navbar-item"> <a href="admin.php " class="navbar-link">admin</a></li>

      
   </ul>
  </div>
  <h2 class="admin-pannel">Admin Panel</h2>

<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blood_donors";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle approval
if (isset($_GET['approve'])) {
    $id = $_GET['approve'];
    $sql = "UPDATE blood_requests SET status='Approved' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        echo "<p>Request approved successfully</p>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

   // Fetch blood requests summary
$totalRequestsSql = "SELECT COUNT(*) AS total FROM blood_requests";
$totalPendingSql = "SELECT COUNT(*) AS pending FROM blood_requests WHERE status='Pending'";
$totalApprovedSql = "SELECT COUNT(*) AS approved FROM blood_requests WHERE status='Approved'";

$totalRequestsResult = $conn->query($totalRequestsSql);
$totalPendingResult = $conn->query($totalPendingSql);
$totalApprovedResult = $conn->query($totalApprovedSql);

$totalRequests = $totalRequestsResult->fetch_assoc()['total'];
$totalPending = $totalPendingResult->fetch_assoc()['pending'];
$totalApproved = $totalApprovedResult->fetch_assoc()['approved'];
?>

<div class="summary">
    <div class="summary-item">
        <h3><?php echo $totalRequests; ?></h3>
        <p>Total Requests</p>
    </div>
    <div class="summary-item">
        <h3><?php echo $totalPending; ?></h3>
        <p>Pending Requests</p>
    </div>
    <div class="summary-item">
        <h3><?php echo $totalApproved; ?></h3>
        <p>Approved Requests</p>
    </div>
</div>

    <!-- donor summery section -->
    <?php
        // Fetch donor counts by blood type
        $bloodTypes = ["A+", "A-", "B+", "B-", "O+", "O-", "AB+", "AB-"];
        $donorCounts = [];

        foreach ($bloodTypes as $bloodType) {
            $sql = "SELECT COUNT(*) AS count FROM donors WHERE blood_type='$bloodType'";
            $result = $conn->query($sql);
            $count = $result->fetch_assoc()['count'];
            $donorCounts[$bloodType] = $count;
        }
        ?>

        <div class="donor-summary">
            <h3>Blood Donor Summary</h3>
            <table class="donor-summary-table">
                <thead>
                    <tr>
                        <th>Blood Type</th>
                        <th>Total Donors</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bloodTypes as $bloodType): ?>
                        <tr>
                            <td><?php echo $bloodType; ?></td>
                            <td><?php echo $donorCounts[$bloodType]; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    

        <?php
        // Fetch pending blood requests
        $sql = "SELECT id, name, blood_type, region, contact, status, created_at FROM blood_requests WHERE status='Pending'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<div class='card'>
                        <h3>Blood Request from {$row['name']}</h3>
                        <p><strong>Blood Type:</strong> {$row['blood_type']}</p>
                        <p><strong>Region:</strong> {$row['region']}</p>
                        <p><strong>Contact:</strong> {$row['contact']}</p>
                        <p><strong>Requested On:</strong> {$row['created_at']}</p>
                        <p><strong>Status:</strong> {$row['status']}</p>
                        <a href='admin.php?approve={$row['id']}' class='btn'>Approve</a>
                      </div>";
            }
        } else {
            echo "<p>No pending requests</p>";
        }

        $conn->close();
        ?>
</body>
</html>