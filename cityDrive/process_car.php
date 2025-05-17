<?php
include 'config.php';

// Handle delete action
if (isset($_GET['delete'])) {
    $carId = $_GET['delete'];
    $result = firestoreRequest('cars', 'DELETE', $carId);
    header('Location: cars.php?message=Car+deleted+successfully');
    exit;
}

// Handle add/edit form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Prepare car data
    $carData = [
        'make' => ['stringValue' => $_POST['make']],
        'model' => ['stringValue' => $_POST['model']],
        'year' => ['integerValue' => (int)$_POST['year']],
        'seater' => ['integerValue' => (int)$_POST['seater']],
        'price' => ['doubleValue' => (float)$_POST['price']],
        'createdAt' => ['timestampValue' => date('c')]
    ];
    
    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $targetDir = "car-images/"; // Update this path
        $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
        $targetFile = $targetDir . $fileName;
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
            $carData['image'] = ['stringValue' => "https://astrovison.com/cityDrive/car-images/" . $fileName];
        }
    } elseif (isset($_POST['carId'])) {
        // Keep existing image if editing and no new image uploaded
        $existingCar = getFirestoreDocument('cars', $_POST['carId']);
        if ($existingCar && isset($existingCar['image'])) {
            $carData['image'] = ['stringValue' => $existingCar['image']];
        }
    }
    
    if (isset($_POST['carId'])) {
        // Update existing car
        $carId = $_POST['carId'];
        $result = firestoreRequest('cars', 'PATCH', $carId, $carData);
        header('Location: cars.php?message=Car+updated+successfully');
    } else {
        // Add new car
        $result = firestoreRequest('cars', 'POST', null, [
            'fields' => $carData
        ]);
        header('Location: cars.php?message=Car+added+successfully');
    }
    exit;
}

header('Location: cars.php');
?>