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
    <title>Manage Users - CityDrive</title>
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
        <h2>Manage Users</h2>
        
        <div class="card">
            <table>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                </tr>
                <?php
                $usersResponse = firestoreRequest('users');
                $users = formatFirestoreData($usersResponse);
                
                if (!empty($users)) {
                    foreach ($users as $id => $user) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($user['firstName'] ?? '') . ' ' . htmlspecialchars($user['lastName'] ?? '') . '</td>';
                        echo '<td>' . htmlspecialchars($user['email'] ?? '') . '</td>';
                        echo '<td>' . htmlspecialchars($user['role'] ?? '') . '</td>';
                        echo '<td>' . date('Y-m-d', $user['createdAt'] ?? time()) . '</td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="4">No users found.</td></tr>';
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>