<?php
session_start();

if (!isset($_SESSION["email"]) || !isset($_SESSION["user_id"])) {
    header("Location: ../error.html");
    exit();
}

$userId = $_SESSION["user_id"];
$candidateId = $_POST["candidate_id"];

$mysqli = require __DIR__ . "/db.php";
require __DIR__ . "/Blockchain.php";

// Check if the user has already voted
$stmt = $mysqli->prepare("SELECT * FROM votes WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $stmt->close();
    
    // User has already voted, log out and redirect to error page
    session_unset();
    session_destroy();
    header("Location: ../already_voted.html");
    exit();
}

$stmt->close();

// Record the vote
$stmt = $mysqli->prepare("INSERT INTO votes (candidate_id, user_id) VALUES (?, ?)");
$stmt->bind_param("ii", $candidateId, $userId);
$stmt->execute();
$stmt->close();

// Get the last block hash
$blockchain = new Blockchain();
$previousHash = $blockchain->getLastBlockHash($mysqli);

// Create the new block
$data = json_encode(['user_id' => $userId, 'candidate_id' => $candidateId]);
$newBlock = $blockchain->createBlock($previousHash, $data);

// Add the new block to the blockchain
$blockchain->addBlockToDatabase($mysqli, $newBlock);

$mysqli->close();

// Destroy the session to log out the user
session_unset();
session_destroy();

// Redirect to the success page
header("Location: ../vote_success.html");
exit();
?>
