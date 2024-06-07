# Online Voting System

An online platform to facilitate secure and transparent voting.


## Installation

1. **Clone the repository:**

   ```bash
   git clone https://github.com/sukumar9024/online-voting-system.git

Navigate to the project directory:

```bash
cd online-voting-system
```
<p>Ensure you have a web server with PHP support.</p> 
<p>You can use XAMPP or WampServer for this purpose.</p>

<h2>Copy the project files to your web server's root directory</h2>

<p>For XAMPP, the root directory is typically C:\xampp\htdocs\.
For WampServer, the root directory is typically C:\wamp64\www\.</p>
Create a database for the project using your preferred database management tool (e.g., phpMyAdmin).

<h2>Import the database schema:</h2>

<p>Open your database management tool.
Create a new database (e.g., online_voting_system).
Import the provided SQL file (database/schema.sql) into the newly created database.</p>

<h2>Configure the database connection:</h2>

<p>Open the config.php file located in the project directory.</p>

<h3>Update the database connection settings with your database details.</h3>

```bash
<?php
$servername = "localhost";
$username = "your-username";
$password = "your-password";
$dbname = "online_voting_system";
?>
```
<h2>Database Schema</h2>
<h3>Below are the SQL tables required for the project:</h3>

<h3>users Table</h3>

```
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```
<h3>candidates Table</h3>

```
CREATE TABLE candidates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

<h3>votes Table</h3>

```
CREATE TABLE votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    candidate_id INT NOT NULL,
    vote_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (candidate_id) REFERENCES candidates(id)
);
```
<h3>blockchain Table</h3>

```
CREATE TABLE blockchain (
    id INT AUTO_INCREMENT PRIMARY KEY,
    previous_hash VARCHAR(64) NOT NULL,
    current_hash VARCHAR(64) NOT NULL,
    vote_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vote_id) REFERENCES votes(id)
);

```

<h2>Table Descriptions</h2>

<h4>users table: Stores user information with unique constraints on username and email.</h4>
<h4>candidates table: Stores candidate information.</h4>
<h4>votes table: Records each vote with foreign keys linking to the users and candidates tables.</h4>
<h4>blockchain table: Records each vote's blockchain information, ensuring the integrity and immutability of the voting process. Each record links to a vote in the votes table.</h4>
<h1>Contact</h1>
If you have any queries regarding the project, feel free to email me at sukumarchintham866@gmail.com.

Project Link: https://github.com/sukumar9024/online-voting-system.git
