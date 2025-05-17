<?php 
session_start();
if (!isset($_SESSION['admin_uid'])) {
    header("Location:login.php");
    exit;
}
include 'config.php'; 

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CityDrive</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>CityDrive Admin Dashboard</h1>
        <nav>
            <ul>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="cars.php">Cars</a></li>
                <li><a href="users.php">Users</a></li>
                <li><a href="bookings.php">Bookings</a></li>
                <li><a href="add_car.php">Add Car</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <h2>Dashboard Overview</h2>
        
        <div class="card">
            <h3>Quick Stats</h3>
            <?php
            // Get counts from Firestore
            $users = formatFirestoreData(firestoreRequest('users'));
            $cars = formatFirestoreData(firestoreRequest('cars'));
            $bookings = formatFirestoreData(firestoreRequest('bookings'));
            
            $userCount = count($users);
            $carCount = count($cars);
            $bookingCount = count($bookings);
            ?>
            <p>Total Users: <?php echo $userCount; ?></p>
            <p>Total Cars: <?php echo $carCount; ?></p>
            <p>Total Bookings: <?php echo $bookingCount; ?></p>
        </div>
        
        <div class="card">
            <h3>Recent Bookings</h3>
            <?php
            if ($bookingCount > 0) {
                // Sort bookings by createdAt (newest first)
                uasort($bookings, function($a, $b) {
                    return $b['createdAt'] - $a['createdAt'];
                });
                
                // Display first 5 bookings
                $recentBookings = array_slice($bookings, 0, 5, true);
                echo '<table>';
                echo '<tr><th>ID</th><th>Car</th><th>User</th><th>Dates</th><th>Status</th></tr>';
                
                foreach ($recentBookings as $id => $booking) {
                    $car = getFirestoreDocument('cars', $booking['carID']);
                    $user = getFirestoreDocument('users', $booking['userID']);
                    
                    echo '<tr>';
                    echo '<td>' . substr($id, 0, 6) . '</td>';
                    echo '<td>' . ($car ? htmlspecialchars($car['make']) . ' ' . htmlspecialchars($car['model']) : 'N/A') . '</td>';
                    echo '<td>' . ($user ? htmlspecialchars($user['firstName']) . ' ' . htmlspecialchars($user['lastName']) : 'N/A') . '</td>';
                    echo '<td>' . date('Y-m-d', $booking['startDate']) . ' to ' . date('Y-m-d', $booking['endDate']) . '</td>';
                    echo '<td>' . htmlspecialchars($booking['status']) . '</td>';
                    echo '</tr>';
                }
                
                echo '</table>';
            } else {
                echo '<p>No recent bookings found.</p>';
            }
            ?>
        </div>
    </div>
</body>
</html>