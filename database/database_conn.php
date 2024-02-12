<?php
$servername = "localhost";
$username = "root"; #default MySQL username & password
$password = "";
$dbname = "guestbook";

# create new db connection
$conn = new mysqli($servername, $username, $password, $dbname);

# check new connection
if ($conn->connect_error) {
    die("Verbindung fehlgeschlagen: " . $conn->connect_errno . ": " . $conn->connect_error);
}

# get all guestboook entries
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json'); #ensures correct result interpretation (in JSON Format)
    $sql = "SELECT * FROM entries ORDER BY created_at DESC"; #latest first
    $stmt = $conn->prepare($sql);
    if ($stmt->execute()) {
        $result = $stmt->get_result();
        $entries = $result->fetch_all(MYSQLI_ASSOC);
        echo json_encode($entries);
    } else {
        echo json_encode(["error" => "Fehler bei der Abfrage"]); #Error message
    }
}

# create new entry
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "INSERT INTO entries (name, message) VALUES (?, ?)"; #prevents SQL injections using prepared statements
    $stmt = $conn->prepare($sql);

    # bind the parameters
    $stmt->bind_param("ss", $name, $message);

    # assign the values
    $name = $_POST['name'] ?? '';
    $message = $_POST['message'] ?? '';

    # validate
    if (empty($name) || empty($message)) {
        echo json_encode(["error" => "Name und Nachricht sind erforderlich"]);
        exit;
    }

    if (strlen($name) > 255) {
        echo json_encode(["error" => "Name ist zu lang"]);
        exit;
    }

    # Clean entry - prevent XSS
    $name = htmlspecialchars(strip_tags($name));
    $message = htmlspecialchars(strip_tags($message));

    #execute
    if ($stmt->execute()) {
        echo json_encode(["success" => "Eintrag erfolgreich hinzugefügt"]);
    } else {
        echo json_encode(["error" => "Fehler beim Hinzufügen des Eintrags"]);
    }
}

$conn->close();
