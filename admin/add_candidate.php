<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if all required fields are provided
    if (empty($_POST["candidate_name"]) || empty($_POST["candidate_party"]) || empty($_FILES["candidate_photo"])) {
        die("All fields are required.");
    }

    // Sanitize inputs
    $candidateName = htmlspecialchars($_POST["candidate_name"]);
    $candidateParty = htmlspecialchars($_POST["candidate_party"]);

    // Upload photo
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($_FILES["candidate_photo"]["name"]);
    $targetDirClient = "../frontend/uploads/";
    $targetFileClient = $targetDirClient . basename($_FILES["candidate_photo"]["name"]); // Corrected variable name

    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES["candidate_photo"]["tmp_name"]);
    if ($check === false) {
        die("File is not an image.");
    }

    // Check file size
    if ($_FILES["candidate_photo"]["size"] > 500000) { // 500KB
        die("Sorry, your file is too large.");
    }

    // Allow certain file formats
    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        die("Sorry, only JPG, JPEG, PNG & GIF files are allowed.");
    }

    // Move uploaded file to destination
    if (!move_uploaded_file($_FILES["candidate_photo"]["tmp_name"], $targetFile)) {
        die("Sorry, there was an error uploading your file.");
    }

    // Database connection
    $mysqli = require __DIR__ . "/db.php"; // Adjust this path as per your configuration

    // Insert into candidates table
    $stmt = $mysqli->prepare("INSERT INTO candidates (name, description, photo, photo_client) VALUES (?, ?, ?, ?)");
    $description =  $candidateParty; // Example description

    if (!$stmt) {
        die("SQL error: " . $mysqli->error);
    }

    $stmt->bind_param("ssss", $candidateName, $description, $targetFile, $targetFileClient);

    if ($stmt->execute()) {
        header("Location: dashboard.html");
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $mysqli->close();
} else {
    echo "Invalid request method.";
}
?>
