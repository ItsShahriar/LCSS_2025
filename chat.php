<?php
session_start();

if (!isset($_GET['topic'])) {
  header('Location: index.php');
  exit;
}

$topic = urldecode($_GET['topic']); // Decode URL-encoded topic

// Database connection (replace with your credentials)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chat_app_db";

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
  echo "Connection failed: " . $e->getMessage();
  exit;
}

// Function to retrieve messages for a specific chat
function getMessages($conn, $topic) {
  $sql = "SELECT sender, message FROM messages WHERE topic = :topic ORDER BY created_at ASC";
  $stmt = $conn->prepare($sql);
  $stmt->bindParam(':topic', $topic, PDO::PARAM_STR);
  $stmt->execute();
  $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $messages;
}

// Handle message submission (if form is submitted)
if (isset($_POST['send_message'])) {
  $sender_name = trim($_POST['sender_name']);
  $message_content = trim($_POST['message_content']);

  if ($sender_name && $message_content) {
    $sql = "INSERT INTO messages (topic, sender, message) VALUES (:topic, :sender, :message)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':topic', $topic, PDO::PARAM_STR);
    $stmt->bindParam(':sender', $sender_name, PDO::PARAM_STR);
    $stmt->bindParam(':message', $message_content, PDO::PARAM_STR);

    try {
      $stmt->execute();
      // Redirect back to chat page to show new message
      header("Location: chat.php?topic=" . urlencode($topic));
      exit;
    } catch(PDOException $e) {
      echo "Error sending message: " . $e->getMessage();
    }
  } else {
    echo "<p style='color: red;'>Please enter your name and a message.</p>";
  }
}

$messages = getMessages($conn, $topic);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatApp - <?php echo $topic; ?> Chat</title>
</head>
<body>
    <h2><?php echo $topic; ?> Chat</h2>

    <?php
    if (empty($messages)) {
      echo "<p>No messages in this chat yet.</p>";
    } else {
      echo "<table>";
      echo "<tr><th>Sender</th><th>Message</th></tr>";
      foreach ($messages as $message) {
        echo "<tr><td>" . $message['sender'] . "</td><td>" . $message['message'] . "</td></tr>";
      }
      echo "</table>";
    }
    ?>

    <h3>Add a Message</h3>
    <form method="post">
        <label for="sender_name">Your Name:</label>
        <input type="text" name="sender_name" id="sender_name" required>
		</br>
		</br>
        <label for="message_content">Message:</label>
        <textarea name="message_content" id="message_content" required></textarea>
		</br>
		</br>
        <button type="submit" name="send_message">Send Message</button>
    </form>
	
	</br>
	</br>
	
	<button onclick="redirectToIndex()">Go to Index</button>

	<script>
	function redirectToIndex() {
	  window.location.href = 'index.php';
	}
	</script>
	
</body>
</html>
