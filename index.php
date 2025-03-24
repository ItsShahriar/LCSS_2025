<?php
session_start();

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

// Function to retrieve existing chats
function getChats($conn) {
  $sql = "SELECT topic FROM chats";
  $stmt = $conn->prepare($sql);
  $stmt->execute();
  $chats = $stmt->fetchAll(PDO::FETCH_ASSOC);
  return $chats;
}

if (isset($_POST['create_chat'])) {
  $topic = trim($_POST['chat_topic']);

  if ($topic) {
    $sql = "INSERT INTO chats (topic) VALUES (:topic)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':topic', $topic, PDO::PARAM_STR);

    try {
      $stmt->execute();
      $_SESSION['message'] = "Chat created successfully!";
    } catch(PDOException $e) {
      echo "Error creating chat: " . $e->getMessage();
    }
  } else {
    $_SESSION['error'] = "Please enter a valid chat topic.";
  }
}

$chats = getChats($conn);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ChatApp</title>
</head>
<body>
    <h1>ChatApp</h1>

    <?php
    // Display success/error messages
    if (isset($_SESSION['message'])) {
      echo "<p style='color: green;'>".$_SESSION['message']."</p>";
      unset($_SESSION['message']);
    }
    if (isset($_SESSION['error'])) {
      echo "<p style='color: red;'>".$_SESSION['error']."</p>";
      unset($_SESSION['error']);
    }
    ?>

    <h2>Create a new chat</h2>
    <form method="post">
        <label for="chat_topic">Chat Topic:</label>
        <input type="text" name="chat_topic" id="chat_topic" required>
        <button type="submit" name="create_chat">Create Chat</button>
    </form>

    <h2>Existing Chats</h2>
    <?php if (empty($chats)) : ?>
        <p>No chats created yet.</p>
    <?php else : ?>
        <ul>
        <?php foreach ($chats as $chat) : ?>
            <li><a href="chat.php?topic=<?php echo urlencode($chat['topic']); ?>"><?php echo $chat['topic']; ?></a></li>
        <?php endforeach; ?>
        </ul>
    <?php endif; ?>

</body>
</html>
