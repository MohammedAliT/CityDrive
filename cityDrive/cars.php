<?php 
session_start();
if (!isset($_SESSION['admin_uid'])) {
    header("Location:login.php");
    exit;
}
include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Cars - CityDrive</title>
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
        <h2>Manage Cars</h2>
        
        <?php if (isset($_GET['message'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['message']); ?></div>
        <?php endif; ?>
        
        <div class="card">
            <table>
                <tr>
                    <th>Image</th>
                    <th>Make</th>
                    <th>Model</th>
                    <th>Year</th>
                    <th>Seater</th>
                    <th>Price</th>
                    <th>Actions</th>
                </tr>
                <?php
                $carsResponse = firestoreRequest('cars');
                $cars = formatFirestoreData($carsResponse);
                
                if (!empty($cars)) {
                    foreach ($cars as $id => $car) {
                        echo '<tr>';
                        echo '<td><img src="' . htmlspecialchars($car['image'] ?? '') . '" alt="Car Image" style="max-width: 100px;"></td>';
                        echo '<td>' . htmlspecialchars($car['make'] ?? '') . '</td>';
                        echo '<td>' . htmlspecialchars($car['model'] ?? '') . '</td>';
                        echo '<td>' . htmlspecialchars($car['year'] ?? '') . '</td>';
                        echo '<td>' . htmlspecialchars($car['seater'] ?? '') . '</td>';
                        echo '<td>$' . htmlspecialchars($car['price'] ?? '') . '</td>';
                        echo '<td>
                                <a href="add_car.php?edit=' . urlencode($id) . '" class="btn">Edit</a>
                                <a href="process_car.php?delete=' . urlencode($id) . '" class="btn" onclick="return confirm(\'Are you sure?\')">Delete</a>
                              </td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="7">No cars found.</td></tr>';
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>