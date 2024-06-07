<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if all required fields are provided
    if (empty($_POST["candidate_name"]) || empty($_POST["candidate_party"]) || empty($_FILES["candidate_photo"])) {
        echo "<script>alert('All fields are required')</script>";
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
        echo "<script>alert('File is not an image.')</script>";
    }

    // Check file size
    if ($_FILES["candidate_photo"]["size"] > 500000) { // 500KB
        echo "<script>alert('Sorry, your file is too large.')</script>";
    }

    // Allow certain file formats
    if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
        echo "<script>alert('Sorry, only JPG, JPEG, PNG & GIF files are allowed.')</script>";
    }

    // Move uploaded file to destination
    if (!move_uploaded_file($_FILES["candidate_photo"]["tmp_name"], $targetFile)) {
        echo "<script>alert('Sorry, there was an error uploading your file.')</script>";
    }

    // Database connection
    $mysqli = require __DIR__ . "/db.php"; // Adjust this path as per your configuration

    // Insert into candidates table
    $stmt = $mysqli->prepare("INSERT INTO candidates (name, description, photo, photo_client) VALUES (?, ?, ?, ?)");
    $description =  $candidateParty; // Example description

    if (!$stmt) {
        echo "<script>alert('Error Registering. Contact administrator.')</script>";
    }

    $stmt->bind_param("ssss", $candidateName, $description, $targetFile, $targetFileClient);

    if ($stmt->execute()) {
        header("Location: dashboard.html");
    } else {
        echo "Error ";
    }

    $stmt->close();
    $mysqli->close();
} else {
    die();
}
?>
