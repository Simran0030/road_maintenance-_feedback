<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$conn = new mysqli('localhost', 'root', '', 'feedback_data');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Query to get the 5 most recent feedback entries along with the image path
$result = $conn->query("SELECT name, city, state, zip, location, feedback, urgency, Location_Image FROM road_maintenence_data ORDER BY SR_No DESC LIMIT 5");

if (!$result) {
    die('Query failed: ' . $conn->error);
}

$feedbacks = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Check if an image path exists and if the image file exists
        if (file_exists($row['Location_Image'])) {
            // Get image content and encode it in base64
            $imageData = base64_encode(file_get_contents($row['Location_Image']));
            $row['Location_Image'] = 'data:image/jpeg;base64,' . $imageData; // Change 'jpeg' based on your image type
        } else {
            $row['Location_Image'] = '';  // Default text if image not found
        }
        $feedbacks[] = $row;
    }
}

// Return feedbacks as JSON
echo json_encode($feedbacks);

// Close connection
$conn->close();
?>