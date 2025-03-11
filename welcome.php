<?php
session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['username'])) {
    header("Location: auth.php");
    exit();
}

$username = $_SESSION['username'];

// Database connection
$servername = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "login_system";

$conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get list of users
$users = [];
$stmt = $conn->prepare("SELECT username FROM users WHERE username != ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $users[] = $row['username'];
}

$stmt->close();
?>
<!DOCTYPE HTML>
<html>
<head>
    <title>Welcome - Chat</title>
    <style>
        #chat-box {
            border: 1px solid #ccc;
            padding: 10px;
            height: 300px;
            overflow-y: scroll;
        }
    </style>
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
    <p>Start chatting with other users.</p>

    <!-- Chat Selection -->
    <h3>Select User to Chat With</h3>
    <form method="GET" action="">
        <label for="receiver">Choose User:</label>
        <select id="receiver" name="receiver" required>
            <option value="">Select a user</option>
            <?php foreach ($users as $user) : ?>
                <option value="<?php echo htmlspecialchars($user); ?>">
                    <?php echo htmlspecialchars($user); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Chat</button>
    </form>

    <?php if (isset($_GET['receiver'])): ?>
        <?php
        $receiver = $_GET['receiver'];

        // Fetch messages
        $stmt = $conn->prepare("
            SELECT sender, message, timestamp 
            FROM messages 
            WHERE (sender = ? AND receiver = ?) 
               OR (sender = ? AND receiver = ?) 
            ORDER BY timestamp ASC
        ");
        $stmt->bind_param("ssss", $username, $receiver, $receiver, $username);
        $stmt->execute();
        $messages = $stmt->get_result();
        $stmt->close();
        ?>
        <h3>Chat with <?php echo htmlspecialchars($receiver); ?></h3>
        <div id="chat-box">
            <?php while ($msg = $messages->fetch_assoc()): ?>
                <p>
                    <strong><?php echo htmlspecialchars($msg['sender']); ?>:</strong>
                    <?php echo htmlspecialchars($msg['message']); ?>
                    <em>(<?php echo $msg['timestamp']; ?>)</em>
                </p>
            <?php endwhile; ?>
        </div>

        <!-- Send Message -->
        <form method="POST" action="send_message.php">
            <input type="hidden" name="receiver" value="<?php echo htmlspecialchars($receiver); ?>">
            <textarea name="message" placeholder="Type your message..." required></textarea><br>
            <button type="submit">Send</button>
        </form>
    <?php endif; ?>

    <hr>
    <form method="POST" action="logout.php">
        <button type="submit">Logout</button>
    </form>
</body>
</html>
<?php
$conn->close();
?>
