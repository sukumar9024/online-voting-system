<?php
session_start();

// Check if admin is not logged in, redirect to error page or login page
if (!isset($_SESSION["admin_logged_in"]) || $_SESSION["admin_logged_in"] !== true) {
    header("Location: ./error.html");
    exit();
}

// Logout functionality
if (isset($_GET['logout'])) {
    session_unset();
    session_destroy();
    header("Location: ./admin.html");
    exit();
}

require_once 'Blockchain.php'; // Adjust path as per your file structure

$blockchain = new Blockchain();
$mysqli = require 'db.php'; // Adjust path as per your file structure

// Function to parse data from each block
function parseBlockData($blockData) {
    return json_decode($blockData, true); // Assuming data is stored as JSON
}

// Initialize variables to store election results
$candidates = []; // Initialize as empty array

// Fetch blocks from the database and process them
$blocks = [];
$currentHash = $blockchain->getLastBlockHash($mysqli);

while ($currentHash !== null) {
    // Retrieve block data from database using $currentHash
    $stmt = $mysqli->prepare("SELECT data, previous_hash FROM blockchain WHERE block_hash = ?");
    $stmt->bind_param("s", $currentHash);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $blockData = $result->fetch_assoc();

        // Parse and process block data
        $parsedData = parseBlockData($blockData['data']);

        // Example: Increment candidate vote counts based on parsed data
        if (isset($parsedData['candidate_id'])) {
            $candidateId = $parsedData['candidate_id'];

            // Fetch candidate information from the database
            $stmtCandidate = $mysqli->prepare("SELECT id, name, description, photo FROM candidates WHERE id = ?");
            $stmtCandidate->bind_param("i", $candidateId);
            $stmtCandidate->execute();
            $resultCandidate = $stmtCandidate->get_result();

            if ($resultCandidate->num_rows > 0) {
                $candidate = $resultCandidate->fetch_assoc();
                $candidateName = $candidate['name'];
                $candidateParty = $candidate['description'];
                $candidateId = $candidate['id'];
                $candidatePhoto = $candidate['photo'];

                // Store candidate information and votes
                if (!isset($candidates[$candidateId])) {
                    $candidates[$candidateId] = [
                        'name' => $candidateName,
                        'party' => $candidateParty,
                        'votes' => 0,
                        'id' => $candidateId,
                        'photo' => $candidatePhoto
                    ];
                }
                $candidates[$candidateId]['votes']++;
            }

            $stmtCandidate->close();
        }

        // Move to the previous block using previous_hash
        $currentHash = $blockData['previous_hash'];
    } else {
        // Handle case where no block data is found
        break; // Exit the loop if no more blocks are found
    }

    $stmt->close();
}

// Close database connection or do any necessary cleanup
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Results</title>
    <!-- <link rel="stylesheet" href="../frontend/styles/">  -->
    <style>
        body{
            background-image: url(../frontend/styles/images/results.jpg);
        }

        h2 {
            width: 100%;
            text-align: center;
            color: lightyellow;
        }

        .candidate-card {
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 20px;
            margin-bottom: 10px;
            width: 300px;
            background-color: #ffffff;
        }

        .candidate-card img {
            max-width: 100%;
            height: auto;
            border-radius: 50%;
        }

        .results-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .candidates-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }

        .candidate-card {
            border: 1px solid #ccc;
            padding: 10px;
            margin-bottom: 20px;
            width: calc(33.33% - 20px); /* Adjust width for 3 cards per row with margins */
            box-sizing: border-box;
            text-align: center;
            box-shadow: rgba(50, 50, 93, 0.25) 0px 30px 60px -12px, rgba(0, 0, 0, 0.3) 0px 18px 36px -18px;
        }

        .candidate-card img {
            width: 100px; /* Set image width */
            height: 100px; /* Set image height */
            object-fit: cover; /* Maintain aspect ratio and cover container */
            border-radius: 50%; /* Makes the image circular */
        }

        .candidate-card h3 {
            margin-top: 10px;
            margin-bottom: 5px;
        }

        .candidate-card p {
            margin: 5px 0;
        }

        /* Added styles for navigation bar */
        nav {
            background-color: #ccc;
            padding: 10px 0;
            text-align: center;
        }

        nav ul {
            display: flex;
            justify-content: center;
            gap: 1rem;
            list-style: none;
        }

        nav ul li a {
            text-decoration: none;
            color: #333;
            padding: 10px 15px;
            border-radius: 5px;
            background-color: #ddd;
            transition: background-color 0.3s;
        }

        nav ul li a:hover {
            background-color: #ccc;
        }

        .logout-button {
            margin-left: 10px;
            padding: 10px 15px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .logout-button:hover {
            background-color: #d32f2f;
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav>
        <ul>
            <li><a href="#">Election Results</a></li>
            <li><a href="./admin.html">Admin Panel</a></li>
            <li><a href="?logout=true" class="logout-button">Logout</a></li>
        </ul>
    </nav>

    <div class="results-container">
        <h2>Election Results</h2>
        <div class="candidates-container">
            <?php
            // Display candidates and their vote counts
            if (!empty($candidates)) {
                foreach ($candidates as $candidateId => $candidate) {
                    echo '<div class="candidate-card">';
                    echo "<h3>{$candidate['name']}</h3>";
                    echo "<p>Party: {$candidate['party']}</p>";
                    echo "<p>Votes: {$candidate['votes']}</p>";
                    
                    if ($candidate['photo']) {
                        echo "<img src='{$candidate['photo']}' alt='{$candidate['name']}' class='candidate-photo'>";
                    } else {
                        echo "<p>No photo available</p>";
                    }

                    echo '</div>';
                }
            } else {
                echo "<p>No candidates found.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
