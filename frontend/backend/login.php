<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
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
            // Check if the user has already voted
            $stmt->close(); // Close the previous statement

            $stmt = $mysqli->prepare("SELECT * FROM votes WHERE user_id = ?");
            if (!$stmt) {
                header("Location: ../login.html?message=" . urlencode("SQL error: " . $mysqli->error));
                exit;
            }

            $stmt->bind_param("i", $user["id"]);
            $stmt->execute();
            $voteResult = $stmt->get_result();

            if ($voteResult->num_rows > 0) {
                // User has already voted
                header("Location: ../login.html?message=" . urlencode("You have already cast your vote. You cannot log in again."));
                exit;
            } else {
                // User has not voted, start session
                session_start();
                $_SESSION["email"] = $user["email"];
                $_SESSION["user_id"] = $user["id"];
                header("Location: ../voter.php");
                exit;
            }
        } else {
            header("Location: ../login.html?message=" . urlencode("Incorrect password."));
            exit;
        }
    } else {
        header("Location: ../login.html?message=" . urlencode("User not found."));
        exit;
    }

    $stmt->close(); // Close statement
    $mysqli->close(); // Close database connection
} else {
    header("Location: ../login.html?message=" . urlencode("Invalid request method."));
    exit;
}
?>
