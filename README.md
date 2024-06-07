# Online Voting System

An online platform to facilitate secure and transparent voting.

## Installation

1. Clone the repository:

   ```bash
   git clone https://github.com/sukumar9024/online-voting-system.git
   Navigate to the project directory:

bash
Copy code
cd online-voting-system
Ensure you have a web server with PHP support. You can use XAMPP or WampServer for this purpose.

Copy the project files to your web server's root directory:

For XAMPP, the root directory is typically C:\xampp\htdocs\.
For WampServer, the root directory is typically C:\wamp64\www\.
Create a database for the project using your preferred database management tool (e.g., phpMyAdmin).

Import the database schema:

Open your database management tool.
Create a new database (e.g., online_voting_system).
Import the provided SQL file (database/schema.sql) into the newly created database.
Configure the database connection:

Open the config.php file located in the project directory.

Update the database connection settings with your database details.

php
Copy code
<?php
$servername = "localhost";
$username = "your-username";
$password = "your-password";
$dbname = "online_voting_system";
?>


Below are the SQL tables required for the project.
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE candidates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    candidate_id INT NOT NULL,
    vote_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (candidate_id) REFERENCES candidates(id)
);

CREATE TABLE blockchain (
    id INT AUTO_INCREMENT PRIMARY KEY,
    previous_hash VARCHAR(64) NOT NULL,
    current_hash VARCHAR(64) NOT NULL,
    vote_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vote_id) REFERENCES votes(id)
);

users table: Stores user information with unique constraints on username and email.
candidates table: Stores candidate information.
votes table: Records each vote with foreign keys linking to the users and candidates tables.
blockchain table: Records each vote's blockchain information, ensuring the integrity and immutability of the voting process. Each record links to a vote in the votes table.

If have any queries regarding the project feel free to mail to sukumarchintham866@gmail.com
