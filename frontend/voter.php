<?php
session_start();

if (!isset($_SESSION["email"])) {
    header("Location: ./error.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voter Portal - Vote Page</title>
    <link rel="stylesheet" href="./styles/vote.css"> 
    <style>
        .dialog-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            visibility: hidden;
        }

        .dialog {
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
            width: 300px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .dialog-buttons {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }

        .dialog-button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .dialog-button.confirm {
            background-color: #4CAF50;
            color: white;
        }

        .dialog-button.cancel {
            background-color: #f44336;
            color: white;
        }
    </style>
    <script>
        function showDialog(candidateName, form) {
            const dialogOverlay = document.getElementById('dialog-overlay');
            const dialogMessage = document.getElementById('dialog-message');
            const confirmButton = document.getElementById('dialog-confirm');

            dialogMessage.textContent = `Would you like to vote for ${candidateName}?`;
            confirmButton.onclick = function() {
                form.submit();
            };

            dialogOverlay.style.visibility = 'visible';
        }

        function hideDialog() {
            const dialogOverlay = document.getElementById('dialog-overlay');
            dialogOverlay.style.visibility = 'hidden';
        }
    </script>
</head>
<body>

    <nav>
        <marquee behavior="scroll" direction="left"><h1>Welcome to the Voter Portal..!!</h1></marquee>
    </nav>
    <h2 class="heading">Vote for your candidate..!!</h2>
    <div class="candidates-container">
        <?php
            // Include database connection
            $mysqli = require __DIR__ . "/db.php";

            // Query candidates from database
            $sql = "SELECT id, name, description, photo FROM candidates";
            $result = $mysqli->query($sql);

            // Initialize an array to store candidates
            $candidates = [];

            // Check if there are candidates
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Store candidate details in the array
                    $candidates[] = [
                        'id' => $row['id'],
                        'name' => $row['name'],
                        'description' => $row['description'],
                        'photo' => $row['photo']
                    ];
                }
                $result->free();
            } else {
                echo "<p>No candidates available</p>";
            }

            // Close database connection
            $mysqli->close();

            // Display candidates
            foreach ($candidates as $candidate) {
                echo '<div class="candidate-card">';
                echo '<img src="' . htmlspecialchars($candidate['photo']) . '" alt="Candidate Photo" />';
                echo '<h2>' . htmlspecialchars($candidate['name']) . '</h2>';
                echo '<p>' . htmlspecialchars($candidate['description']) . '</p>';
                echo '<form action="./backend/vote_handler.php" method="post" onsubmit="event.preventDefault(); showDialog(\'' . htmlspecialchars(addslashes($candidate['name'])) . '\', this);">';
                echo '<input type="hidden" name="candidate_id" value="' . htmlspecialchars($candidate['id']) . '">';
                echo '<button type="submit" class="vote-button">Vote</button>';
                echo '</form>';
                echo '</div>';
            }
        ?>
    </div>

    <!-- Custom Dialog HTML -->
    <div id="dialog-overlay" class="dialog-overlay">
        <div class="dialog">
            <p id="dialog-message"></p>
            <div class="dialog-buttons">
                <button id="dialog-confirm" class="dialog-button confirm">Yes</button>
                <button class="dialog-button cancel" onclick="hideDialog()">No</button>
            </div>
        </div>
    </div>

</body>
</html>
