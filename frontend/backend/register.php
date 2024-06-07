<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Check if the username is provided
    if (empty($_POST["username"])) {
        echo "<script>alert('Name is required')</script>";
    }
    
    // Validate email
    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
        echo "<script>alert('Valid email is required')</script>";
    }
    
    // Check password length
    if (strlen($_POST["password"]) < 4) {
        echo "<script>alert('Password must be at least 4 characters')</script>";
    }
    
    // Ensure password contains at least one letter
    if (!preg_match("/[a-z]/i", $_POST["password"])) {
        echo "<script>alert('Password must contain at least one letter')</script>";
    }
    
    // Ensure password contains at least one number
    if (!preg_match("/[0-9]/", $_POST["password"])) {
        echo "<script>alert('Password must contain at least one number')</script>";
    }
    
    // Check if passwords match
    if ($_POST["password"] !== $_POST["re_password"]) {
        echo "<script>alert('Passwords must match')</script>";
    }
    
    // Check if mobile number is provided
    if (empty($_POST["mobile"])) {
        echo "<script>alert('Number is required.')</script>";
    }

    // Sanitize inputs
    $username = filter_var($_POST["username"], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    $mobile = filter_var($_POST["mobile"], FILTER_SANITIZE_STRING);

    // Hash the password
    $hashed_password = password_hash($_POST["password"], PASSWORD_DEFAULT);

    $mysqli = require __DIR__ . "/db.php";

    $sql = "INSERT INTO users (username, email, password, mobile) VALUES (?, ?, ?, ?)";
    
    $stmt = $mysqli->stmt_init();

    if (!$stmt->prepare($sql)) {
        echo "<script>alert('Error Registering. Contact Administrator.')</script>";
    }

    $stmt->bind_param("ssss", $username, $email, $hashed_password, $mobile);
    
    if ($stmt->execute()) {
        header("Location: ../../index.html");
        exit;
    } else {
        if ($mysqli->errno === 1062) {
            die("Email already taken");
        } else {
            die("Database error: " . $mysqli->error . " (" . $mysqli->errno . ")");
        }
    }

    $stmt->close();
    $mysqli->close();
} else {
    echo "Invalid request method.";
}
?>
