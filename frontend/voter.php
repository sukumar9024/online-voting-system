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
    <link rel="stylesheet" href="vote.css"> <!-- Link to your CSS file -->
</head>
<body>

    <nav>
        <marquee behavior="scroll" direction="left"><h1>Welcome to the Voter Portal..!!</h1></marquee>
    </nav>
    <h2 class="heading">Vote for your candidate..!!</h2>
    <div class="candidates-container">
        <div class="candidate-cards">
            <?php
                // Fetch candidates from database and display in cards
                $mysqli = require __DIR__ . "/db.php";

                $sql = "SELECT id, name, description, photo FROM candidates";
                $result = $mysqli->query($sql);

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '<div class="candidate-card">';
                        echo '<img src="' . htmlspecialchars($row['photo']) . '" alt="Candidate Photo" style="width:100px; height:100px;" />';
                        echo '<h2>' . htmlspecialchars($row['name']) . '</h2>';
                        echo '<p>' . htmlspecialchars($row['description']) . '</p>';
                        echo '<form action="./backend/vote_handler.php" method="post">';
                        echo '<input type="hidden" name="candidate_id" value="' . htmlspecialchars($row['id']) . '">';
                        echo '<button type="submit" class="vote-button">Vote</button>';
                        echo '</form>';
                        echo '</div>';
                    }
                    $result->free();
                } else {
                    echo "<p>No candidates available</p>";
                }

                $mysqli->close();
            ?>
        </div>
    </div>

</body>
</html>
