<?php 
session_start();
if (!isset($_SESSION['admin_uid'])) {
    header("Location:login.php");
    exit;
}
include 'config.php'; 

// Handle status update
// Handle status update - alternative approach
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $bookingId = $_POST['booking_id'];
    $newStatus = $_POST['new_status'];
    
    // Get the current booking
    $currentBooking = getFirestoreDocument('bookings', $bookingId);
    if (!$currentBooking) {
        $error = "Booking not found!";
    } else {
        // Prepare the full document data with only the status changed
        $data = [];
        foreach ($currentBooking as $field => $value) {
            if (is_string($value)) {
                $data[$field] = ['stringValue' => $field === 'status' ? $newStatus : $value];
            } elseif (is_int($value)) {
                $data[$field] = ['integerValue' => $value];
            } elseif (is_float($value)) {
                $data[$field] = ['doubleValue' => $value];
            } else {
                $data[$field] = $value; // Handle other types as needed
            }
        }
        
        $result = firestoreRequest('bookings', 'PATCH', $bookingId, $data);
        
        if (isset($result['error'])) {
            $error = "Failed to update status: " . $result['error']['message'];
        } else {
            $success = "Status updated successfully!";
            header("Location: bookings.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - CityDrive</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .status-form {
            display: inline-block;
        }
        .status-select {
            padding: 4px;
            border-radius: 4px;
        }
       
    </style>
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
        <h2>Manage Bookings</h2>
        
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if (isset($success)): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <div class="card">
            <table>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Car</th>
                    <th>Dates</th>
                    <th>City</th>
                    <th>Total Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                <?php
                $bookingsResponse = firestoreRequest('bookings');
                $bookings = formatFirestoreData($bookingsResponse);
                
                if (!empty($bookings)) {
                    foreach ($bookings as $id => $booking) {
                        $user = getFirestoreDocument('users', $booking['userID']);
                        $car = getFirestoreDocument('cars', $booking['carID']);
                        
                        echo '<tr>';
                        echo '<td>' . substr($id, 0, 6) . '</td>';
                        echo '<td>' . ($user ? htmlspecialchars($user['firstName']) . ' ' . htmlspecialchars($user['lastName']) : 'N/A') . '</td>';
                        echo '<td>' . ($car ? htmlspecialchars($car['make'] . ' ' . $car['model']) : 'N/A') . '</td>';
                        echo '<td>' . date('Y-m-d', $booking['startDate']) . ' to ' . date('Y-m-d', $booking['endDate']) . '</td>';
                        echo '<td>' . htmlspecialchars($booking['city'] ?? '') . '</td>';
                        echo '<td>$' . htmlspecialchars($booking['totalPrice'] ?? '') . '</td>';
                        echo '<td>' . htmlspecialchars($booking['status'] ?? '') . '</td>';
                        echo '<td>
                            <form class="status-form" method="POST">
                                <input type="hidden" name="booking_id" value="' . htmlspecialchars($id) . '">
                                <select name="new_status" class="status-select">
                                    <option value="pending"' . ($booking['status'] === 'pending' ? ' selected' : '') . '>Pending</option>
                                    <option value="confirmed"' . ($booking['status'] === 'confirmed' ? ' selected' : '') . '>Confirmed</option>
                                    <option value="completed"' . ($booking['status'] === 'completed' ? ' selected' : '') . '>Completed</option>
                                    <option value="cancelled"' . ($booking['status'] === 'cancelled' ? ' selected' : '') . '>Cancelled</option>
                                </select>
                                <button type="submit" name="update_status" class="btn">Update</button>
                            </form>
                        </td>';
                        echo '</tr>';
                    }
                } else {
                    echo '<tr><td colspan="8">No bookings found.</td></tr>';
                }
                ?>
            </table>
        </div>
    </div>
</body>
</html>