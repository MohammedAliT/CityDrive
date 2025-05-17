<?php 
session_start();
if (!isset($_SESSION['admin_uid'])) {
    header("Location:login.php");
    exit;
}
include 'config.php';

$editMode = false;
$carData = [];

if (isset($_GET['edit'])) {
    $carId = $_GET['edit'];
    $carData = getFirestoreDocument('cars', $carId);
    if ($carData) {
        $editMode = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $editMode ? 'Edit' : 'Add'; ?> Car - CityDrive</title>
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
        <h2><?php echo $editMode ? 'Edit Car' : 'Add New Car'; ?></h2>
        
        <div class="card">
            <form action="process_car.php" method="POST" enctype="multipart/form-data">
                <?php if ($editMode): ?>
                    <input type="hidden" name="carId" value="<?php echo htmlspecialchars($_GET['edit']); ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="make">Make</label>
                    <input type="text" id="make" name="make" required 
                           value="<?php echo $editMode ? htmlspecialchars($carData['make'] ?? '') : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="model">Model</label>
                    <input type="text" id="model" name="model" required 
                           value="<?php echo $editMode ? htmlspecialchars($carData['model'] ?? '') : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="year">Year</label>
                    <input type="text" id="year" name="year" required 
                           value="<?php echo $editMode ? htmlspecialchars($carData['year'] ?? '') : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="seater">Seater</label>
                    <input type="number" id="seater" name="seater" required 
                           value="<?php echo $editMode ? htmlspecialchars($carData['seater'] ?? '') : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="price">Price per day ($)</label>
                    <input type="number" step="0.01" id="price" name="price" required 
                           value="<?php echo $editMode ? htmlspecialchars($carData['price'] ?? '') : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="image">Car Image</label>
                    <input type="file" id="image" name="image" <?php echo $editMode ? '' : 'required'; ?>>
                    <?php if ($editMode && !empty($carData['image'])): ?>
                        <p>Current image: <a href="<?php echo htmlspecialchars($carData['image']); ?>" target="_blank">View</a></p>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="btn"><?php echo $editMode ? 'Update Car' : 'Add Car'; ?></button>
            </form>
        </div>
    </div>
</body>
</html>