<?php
session_start();

// Define the expected username and password
$expected_username = "admin@admin.com";
$expected_password = "e-voting-systemPassword";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Retrieve user inputs
    $username = $_POST["email"];
    $password = $_POST["password"];

    // Validate username and password
    if ($username === $expected_username && $password === $expected_password) {
        // Username and password are correct, start session
        $_SESSION["admin_logged_in"] = true; // Set admin logged in flag
        header("Location: ./dashboard.html"); // Redirect to dashboard or another page
        exit;
    } else {
        $error_message = "Invalid username or password";
    }
}

// If already logged in, redirect to dashboard
if (isset($_SESSION["admin_logged_in"]) && $_SESSION["admin_logged_in"] === true) {
    header("Location: ./dashboard.html");
    exit;
}
?>