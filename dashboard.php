<?php
session_start();
require 'db.connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT u.username, u.email, p.full_name, p.contact_number, c.course_name
    FROM users u
    LEFT JOIN profiles p ON u.user_id = p.user_id
    LEFT JOIN enrollments e ON u.user_id = e.user_id
    LEFT JOIN courses c ON e.course_id = c.course_id
    WHERE u.user_id = ?
");
$stmt->execute([$user_id]);
$userData = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userData) {
    echo "<p>User not found. Please contact support.</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="stylesheet" href="styles/style.css" />
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: Arial, sans-serif;
        }

        /* Animated background */
        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background: linear-gradient(45deg, #6a11cb, #2575fc);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
        }

        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            padding: 20px;
            max-width: 800px;
            margin: auto;
            background: rgba(255, 255, 255, 0.9); /* Slightly transparent white */
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        h1 {
            text-align: center;
            color: #333;
        }

        p {
            font-size: 1.1rem;
            color: #555;
        }

        .links {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.2s;
            display: flex;
            align-items: center;
            font-size: 1rem;
        }

        .btn:hover {
            background-color: #0056b3;
            transform: scale(1.05);
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: white;
            min-width: 160px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .modal {
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
    </style>
</head>
<body>
    <div class="background"></div>

    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($userData['username']); ?></h1>
        <p>Email: <?php echo htmlspecialchars($userData['email']); ?></p>
        <p>Full Name: <?php echo htmlspecialchars($userData['full_name']); ?></p>
        <p>Contact Number: <?php echo htmlspecialchars($userData['contact_number']); ?></p>
        <p>Enrolled Course: <?php echo htmlspecialchars($userData['course_name']); ?></p>

        <div class="links">
            <div class="dropdown">
                <button class="btn">Options <i class="fas fa-chevron-down"></i></button>
                <div class="dropdown-content">
                    <a href="profile.php">View Profile</a>
                    <a href="courses.php">Available Courses</a>
                    <a href="enroll.php">View Enrolled Courses</a>
                </div>
            </div>
            <button class="btn" onclick="document.getElementById('logoutModal').style.display='block'">Logout <i class="fas fa-sign-out-alt"></i></button>
            <button class="btn" onclick="location.href='landingpage.php'">Back to Landing Page <i class="fas fa-home"></i></button>
        </div>
    </div>

    <!-- Logout Modal -->
    <div id="logoutModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('logoutModal').style.display='none';">&times;</span>
            <h2>Are you sure you want to logout?</h2>
            <button class="btn btn-danger" onclick="logout()">Yes, logout</button>
            <button class="btn btn-secondary" onclick="document.getElementById('logoutModal').style.display='none';">Cancel</button>
        </div>
    </div>

    <script>
        // Close modal when clicking outside of it
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                document.getElementById('logoutModal').style.display = "none";
            }
        }

        function logout() {
            window.location.href = 'logout.php'; // Adjust with your logout page
        }
    </script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.js"></script>
</body>
</html>