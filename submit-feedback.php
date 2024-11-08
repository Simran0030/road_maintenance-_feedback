<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $city = $_POST['city'] ?? '';
    $state = $_POST['state'] ?? '';
    $zip = $_POST['zip'] ?? '';
    $location = $_POST['location'] ?? '';
    $feedback = $_POST['feedback'] ?? '';
    $urgency = $_POST['urgency'] ?? '';

    // Check if the image is uploaded and without errors
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        // Handle image upload
        $target_dir = "uploads/";
        $image_name = basename($_FILES["photo"]["name"]);
        $target_file = $target_dir . time() . "_" . $image_name;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $uploadOk = 1;

        // Check if image file is a real image or fake image
        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if ($check === false) {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        // Check file size (limit to 5MB)
        if ($_FILES["photo"]["size"] > 5000000) {
            echo "Sorry, your file is too large.";
            $uploadOk = 0;
        }

        // Allow certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
            echo "Sorry, only JPG, JPEG, and PNG files are allowed.";
            $uploadOk = 0;
        }

        // Check if $uploadOk is set to 0 by an error
        if ($uploadOk == 0) {
            echo "Sorry, your file was not uploaded.";
        } else {
            // If everything is ok, try to upload file
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
                // Database connection
                $conn = new mysqli('localhost', 'root', '', 'feedback_data');
                if ($conn->connect_error) {
                    die('Connection failed: ' . $conn->connect_error);
                } else {
                    // Correct SQL query with all columns and values
                    $stmt = $conn->prepare("INSERT INTO road_maintenence_data (name, email, phone, city, state, zip, location, feedback, urgency, Location_image) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    
                    // Correct binding with 10 variables
                    $stmt->bind_param("ssssssssss", $name, $email, $phone, $city, $state, $zip, $location, $feedback, $urgency, $target_file);
                    
                    if ($stmt->execute()) {
                        echo "Feedback submitted successfully.";
                    } else {
                        echo "Error: " . $stmt->error;
                    }

                    $stmt->close();
                    $conn->close();
                }
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        // No image was uploaded or there was an error
        echo "No image was uploaded or an error occurred.";
    }
} else {
    echo "No form data submitted!";
}
?>
