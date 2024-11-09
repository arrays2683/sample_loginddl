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

// Check that the user was found
if (!$userData) {
    echo "<p>User not found. Please contact support.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <style>
        body {
            background-color: #f4f4f4;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .navbar {
            background-color: #343a40;
            padding: 1rem;
            position: sticky;
            top: 0;
            z-index: 1000;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar a {
            color: #fff;
            margin-right: 15px;
            text-decoration: none;
            padding: 8px 15px;
            border-radius: 4px;
            transition: background-color 0.3s, transform 0.2s;
        }

        .navbar a:hover {
            background-color: #495057;
            transform: scale(1.05);
        }

        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            background-color: #343a40;
            margin-top: 8px;
            border-radius: 8px;
            opacity: 0;
            transition: opacity 0.3s, visibility 0.3s;
        }

        .dropdown:hover .dropdown-menu {
            display: block;
            opacity: 1;
            visibility: visible;
        }

        .dropdown-item {
            padding: 10px 15px;
            color: white;
            text-decoration: none;
            display: block;
        }

        .dropdown-item:hover {
            background-color: #495057;
        }

        .navbar-right {
            display: flex;
            align-items: center;
        }

        .content {
            text-align: center;
            padding: 100px 20px;
        }

        .content h1 {
            margin: 0;
            font-size: 2.5rem;
            color: #333;
        }

        .content p {
            font-size: 1.2rem;
            color: #777;
        }

        .logout-modal, .dashboard-modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            animation: animatemodal 0.5s;
        }

        @keyframes animatemodal {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Add styles for feedback button */
        .feedback-button {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 50%;
            padding: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            font-size: 20px;
            transition: background-color 0.3s;
        }

        .feedback-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark">
        <a class="navbar-brand" href="#">Student Profile</a>
        
        <div class="dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="studentPortalDropdown" role="button" aria-expanded="false">
                Student Portal
            </a>
            <div class="dropdown-menu" aria-labelledby="studentPortalDropdown">
                <a class="dropdown-item" href="profile.php">Profile</a>
                <a class="dropdown-item" href="courses.php">Courses</a>
                <a class="dropdown-item" href="feedback.php">Feedback</a>
            </div>
        </div>
        
        <div class="navbar-right">
            <input type="text" class="form-control" placeholder="Search..." style="width:auto; margin-right: 10px;">
            <div class="dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="profileDropdown" role="button" aria-expanded="false">
                    <?php echo htmlspecialchars($userData['username']); ?>
                </a>
                <div class="dropdown-menu" aria-labelledby="profileDropdown">
                    <p class="dropdown-item">Name: <?php echo htmlspecialchars($userData['username']); ?></p>
                    <p class="dropdown-item">Email: <?php echo htmlspecialchars($userData['email']); ?></p>
                </div>
            </div>
            <a class="nav-link" href="#" data-toggle="modal" onclick="document.getElementById('dashboardModal').style.display='block'">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a class="nav-link" href="#" data-toggle="modal" onclick="document.getElementById('logoutModal').style.display='block'">
                <i class="fas fa-sign-out-alt"></i>
            </a>
        </div>
    </nav>

    <div class="content">
        <h1>Welcome, <?php echo htmlspecialchars($userData['username']); ?></h1>
        <p>Email: <?php echo htmlspecialchars($userData['email']); ?></p>
    </div>

    <!-- Feedback button -->
    <button class="feedback-button" onclick="window.location.href='feedback.php'">
        <i class="fas fa-comments"></i>
    </button>

    <!-- Logout Modal -->
    <div id="logoutModal" class="logout-modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('logoutModal').style.display='none';">&times;</span>
            <h2>Are you sure you want to logout?</h2>
            <button class="btn btn-danger" onclick="logout()">Yes, logout</button>
            <button class="btn btn-secondary" onclick="document.getElementById('logoutModal').style.display='none';">Cancel</button>
        </div>
    </div>

    <!-- Dashboard Modal -->
    <div id="dashboardModal" class="dashboard-modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('dashboardModal').style.display='none';">&times;</span>
            <h2>Go to Dashboard</h2>
            <p>Are you sure you want to go to the dashboard?</p>
            <button class="btn btn-primary" onclick="goToDashboard()">Yes, go to Dashboard</button>
            <button class="btn btn-secondary" onclick="document.getElementById('dashboardModal').style.display='none';">Cancel</button>
        </div>
    </div>

    <script>
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            if (event.target.classList.contains('logout-modal') || event.target.classList.contains('dashboard-modal')) {
                document.getElementById('logoutModal').style.display = "none";
                document.getElementById('dashboardModal').style.display = "none";
            }
        }

        function logout() {
            // Redirect to logout script
            window.location.href = 'logout.php'; // Adjust with your logout page
        }
        
        function goToDashboard() {
            // Redirect to dashboard page
            window.location.href = 'dashboard.php'; // Adjust with your dashboard page
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/
