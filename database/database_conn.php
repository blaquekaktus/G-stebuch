<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "guestbook";

// create new db connection
$conn = new mysqli($servername, $username, $password, $dbname);

// test new connection
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_errno . ": " . $conn->connect_error);
}
echo "Verbindung erfolgreich";
