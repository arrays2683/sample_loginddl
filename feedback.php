<?php
session_start();
require 'db.connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userData) {
    echo "<p>User not found. Please contact support.</p>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $feedback = trim($_POST['feedback']);
    if (!empty($feedback)) {
        $stmt = $pdo->prepare("INSERT INTO feedback (user_id, feedback_text, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$user_id, $feedback]);
        $successMessage = "Thank you for your feedback!";
    } else {
        $errorMessage = "Please provide some feedback!";
    }
}

$messagesStmt = $pdo->prepare("
    SELECT f.feedback_text, f.created_at, u.username AS sender 
    FROM feedback f 
    JOIN users u ON f.user_id = u.user_id 
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
");
$messagesStmt->execute([$user_id]);
$messages = $messagesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <style>
        /* Background Animation */
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #74ebd5, #9face6);
            background-size: 200% 200%;
            animation: backgroundAnimate 15s ease infinite;
            margin: 0;
            padding: 0;
        }
        @keyframes backgroundAnimate {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Modal Styling */
        .modal {
            display: block;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            width: 90%;
            max-width: 600px;
            border-radius: 8px;
            position: relative;
            animation: modalAppear 0.5s ease-in-out;
        }
        @keyframes modalAppear {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Icon buttons */
        .icon-buttons {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .icon-buttons a {
            color: #007bff;
            font-size: 20px;
            text-decoration: none;
        }

        /* User Info Styling */
        .user-info p {
            font-size: 16px;
            color: #333;
            margin: 5px 0;
        }

        /* Feedback Form Styling */
        h2 {
            font-size: 20px;
            margin-bottom: 15px;
        }
        textarea {
            width: 100%;
            height: 100px;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            margin-bottom: 15px;
            font-size: 16px;
            resize: none;
        }
        button[type="submit"] {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }
        button[type="submit"]:hover {
            background-color: #218838;
            transform: scale(1.05);
        }

        /* Messages Section Styling */
        .messages {
            margin-top: 30px;
        }
        .message-item {
            background-color: #ffffff;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            position: relative;
            border: 1px solid #ddd;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            max-width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .message-item p {
            margin: 5px 0;
        }
        .message-item small {
            font-size: 0.85rem;
            color: #888;
        }

        /* Action Icons for Feedback Messages */
        .message-actions {
            display: flex;
            gap: 10px;
        }
        .message-actions i {
            cursor: pointer;
            color: #007bff;
            font-size: 16px;
            transition: color 0.2s ease;
        }
        .message-actions i:hover {
            color: #000;
        }

        /* More Dropdown Styling */
        .more-dropdown {
            position: relative;
        }
        .more-dropdown .dropdown-content {
            display: none;
            position: absolute;
            bottom: 30px;
            right: 0;
            background-color: #f9f9f9;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            padding: 10px;
            border-radius: 8px;
            min-width: 150px;
            z-index: 1;
        }
        .more-dropdown:hover .dropdown-content {
            display: block;
        }
        .more-dropdown .dropdown-content p {
            margin: 5px 0;
            cursor: pointer;
        }

        /* Modal Menu Icon */
        .modal-menu-icon {
            position: fixed;
            top: 20px;
            right: 20px;
            font-size: 30px;
            cursor: pointer;
            z-index: 10;
        }

        /* Modal Menu Content */
        .modal-menu {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9;
            justify-content: center;
            align-items: center;
        }
        .modal-menu-content {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            width: 250px;
            text-align: center;
        }
        .modal-menu-content a {
            display: block;
            margin: 15px 0;
            text-decoration: none;
            color: #007bff;
        }
        .modal-menu-content a:hover {
            text-decoration: underline;
        }

        .close-menu {
            font-size: 30px;
            color: #555;
            cursor: pointer;
            position: absolute;
            top: 10px;
            right: 10px;
        }

    </style>
</head>
<body>

<!-- Modal Menu Icon (Hamburger) -->
<div class="modal-menu-icon" onclick="toggleModalMenu()">
    <i class="fas fa-bars"></i>
</div>

<!-- Modal Menu Content -->
<div class="modal-menu" id="modalMenu">
    <div class="modal-menu-content">
        <span class="close-menu" onclick="toggleModalMenu()">×</span>
        <h3>Menu</h3>
        <a href="#">Bump</a>
        <a href="#">Details</a>
        <a href="#">Create Poll</a>
    </div>
</div>

<div class="modal">
    <div class="modal-content">
        <div class="icon-buttons">
            <a href="landingpage.php" title="Back to Landing Page"><i class="fas fa-arrow-left"></i> Back</a>
            <a href="logout.php" title="Logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>

        <!-- User Info -->
        <div class="user-info">
            <p><i class="fas fa-user"></i> <?= htmlspecialchars($userData['username']) ?></p>
            <p><i class="fas fa-envelope"></i> <?= htmlspecialchars($userData['email']) ?></p>
        </div>

        <h2>Feedback</h2>
        
        <?php if (isset($successMessage)) : ?>
            <p class="success-message"><?= htmlspecialchars($successMessage) ?></p>
        <?php endif; ?>
        <?php if (isset($errorMessage)) : ?>
            <p class="error-message"><?= htmlspecialchars($errorMessage) ?></p>
        <?php endif; ?>

        <!-- Feedback Form -->
        <form method="POST" action="feedback.php">
            <textarea name="feedback" placeholder="Your feedback..." required></textarea>
            <button type="submit">Send</button>
        </form>

        <h3>Your Messages</h3>
        <div class="messages">
            <?php if ($messages) : ?>
                <?php foreach ($messages as $message) : ?>
                    <div class="message-item">
                        <div class="message-text">
                            <p><strong><?= htmlspecialchars($message['sender']) ?>:</strong></p>
                            <p><?= htmlspecialchars($message['feedback_text']) ?></p>
                            <p><small>Sent on <?= htmlspecialchars(date("Y-m-d H:i", strtotime($message['created_at']))) ?></small></p>
                        </div>
                        <div class="message-actions">
                            <i class="fas fa-reply" title="Reply"></i>
                            <i class="fas fa-copy" title="Copy"></i>
                            <i class="fas fa-trash-alt" title="Remove"></i>
                            <i class="fas fa-flag" title="Report"></i>

                            <!-- More Options Dropdown -->
                            <div class="more-dropdown">
                                <i class="fas fa-ellipsis-v" title="More"></i>
                                <div class="dropdown-content">
                                    <p>Bump</p>
                                    <p>Details</p>
                                    <p>Create Poll</p>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p>No messages to show.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    function toggleModalMenu() {
        const modalMenu = document.getElementById('modalMenu');
        modalMenu.style.display = modalMenu.style.display === 'flex' ? 'none' : 'flex';
    }
</script>

</body>
</html>
