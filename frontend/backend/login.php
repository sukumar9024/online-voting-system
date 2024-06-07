<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Include database connection
    $mysqli = require __DIR__ . "/db.php";

    // Sanitize and validate email
    if (empty($_POST["email"])) {
        header("Location: ../login.html?message=" . urlencode("Email is required"));
        exit;
    }

    $email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../login.html?message=" . urlencode("Invalid email format"));
        exit;
    }

    // Retrieve user record based on email
    $stmt = $mysqli->prepare("SELECT * FROM users WHERE email = ?");
    if (!$stmt) {
        header("Location: ../login.html?message=" . urlencode("SQL error: " . $mysqli->error));
        exit;
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verify password
        if (isset($_POST["password"]) && password_verify($_POST["password"], $user["password"])) {
            
            // Start session
            session_start();
            $_SESSION["email"] = $user["email"];
            $_SESSION["user_id"] = $user["id"];
            header("Location: ../voter.php");
            exit;
        } else {
            // Incorrect password
            header("Location: ../login.html?message=" . urlencode("Incorrect password."));
            exit;
        }
    } else {
        // User not found
        header("Location: ../login.html?message=" . urlencode("User not found."));
        exit;
    }

    $stmt->close(); // Close statement
    $mysqli->close(); // Close database connection
} else {
    // Invalid request method
    header("Location: ../login.html?message=" . urlencode("Invalid request method."));
    exit;
}
?>
