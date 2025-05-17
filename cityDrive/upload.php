<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $targetDir = "car-images/"; // Update this path
    $targetFile = $targetDir . basename($_FILES['image']['name']);
    
    // Check if file already exists
    if (file_exists($targetFile)) {
        echo json_encode(['error' => 'File already exists']);
        exit;
    }
    
    // Try to upload file
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        $imageUrl = "https://astrovison.com/cityDrive/car-images/" . basename($_FILES['image']['name']);
        echo json_encode(['image' => $imageUrl]);
    } else {
        echo json_encode(['error' => 'Error uploading file']);
    }
} else {
    echo json_encode(['error' => 'Invalid request']);
}
?>