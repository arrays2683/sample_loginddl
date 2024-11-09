<?php
session_start();
require 'db.connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();  
}

$user_id = $_SESSION['user_id'];

// Fetch courses data
$stmt = $pdo->query("SELECT * FROM courses");
$courses = $stmt->fetchAll();

// Fetch user details
$stmt_user = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt_user->execute([$user_id]);
$userData = $stmt_user->fetch(PDO::FETCH_ASSOC);

if (!$userData) {
    echo "<p>User not found. Please contact support.</p>";
    exit();
}

// Handle enrollment
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_id = $_POST['course_id'];

    $stmt_enroll = $pdo->prepare("INSERT INTO enrollments (user_id, course_id) VALUES (?, ?)");
    $stmt_enroll->execute([$user_id, $course_id]);
    echo "Enrolled successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Courses</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.css" />
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #0f4b5c, #0e72d7);
            background-size: 200% 200%;
            animation: backgroundAnimate 15s ease infinite;
        }

        @keyframes backgroundAnimate {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            margin-left: 240px; /* Default sidebar width */
            padding: 20px;
            text-align: center;
            color: white;
        }

        h1 {
            font-size: 36px;
            margin-bottom: 30px;
        }

        .course-item {
            background: #ffffff;
            padding: 20px;
            margin: 10px;
            border-radius: 8px;
            color: #333;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
        }

        .course-item:hover {
            transform: scale(1.05);
        }

        .course-item h2 {
            font-size: 24px;
        }

        .course-item p {
            font-size: 18px;
        }

        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #218838;
        }

        /* Sidebar Styles */
        #sidebar {
            position: fixed;
            top: 0;
            left: -240px;
            width: 240px;
            height: 100%;
            background-color: #333;
            transition: left 0.3s ease;
            z-index: 1000;
        }

        #sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        #sidebar ul li {
            padding: 20px;
            border-bottom: 1px solid #444;
        }

        #sidebar ul li a {
            color: white;
            text-decoration: none;
            display: block;
        }

        #sidebar ul li a:hover {
            background-color: #575757;
        }

        #sidebarToggle {
            font-size: 24px;
            color: white;
            cursor: pointer;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1100;
        }

        /* Navbar Styles */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #0e72d7;
            padding: 10px;
            z-index: 100;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        nav a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            font-size: 18px;
        }

        nav a:hover {
            background-color: #333;
        }

        .search-bar input {
            padding: 5px;
            font-size: 16px;
            width: 200px;
        }

        /* Profile Dropdown */
        .profile-dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #333;
            min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .dropdown-content a {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #575757;
        }

        .profile-dropdown:hover .dropdown-content {
            display: block;
        }

        /* Dark Mode */
        body.dark-mode {
            background: #333;
            color: #fff;
        }

        /* Modal Styling */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            width: 80%;
            max-width: 500px;
            border-radius: 8px;
        }

        .modal-close {
            cursor: pointer;
            color: #aaa;
            position: absolute;
            top: 10px;
            right: 20px;
        }

        .modal-close:hover {
            color: black;
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div id="sidebar">
    <ul>
        <li><a href="dashboard.php">Dashboard</a></li>
        <li><a href="landingpage.php">Landing Page</a></li>
        <li><a href="enroll.php">Enrollment</a></li>
        <li><a href="courses.php">Courses</a></li>
        <li><a href="feedback.php">Feedback</a></li>
    </ul>
</div>

<!-- Toggle Sidebar Button -->
<i id="sidebarToggle" class="fas fa-bars"></i>

<!-- Navbar -->
<nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="landingpage.php">Landing Page</a>
    <a href="enroll.php">Enrollment</a>
    <a href="courses.php">Courses</a>
    <a href="feedback.php">Feedback</a>
    
    <!-- Search Bar -->
    <div class="search-bar">
        <input type="text" id="courseSearch" placeholder="Search courses...">
    </div>

    <!-- Dark Mode Toggle -->
    <span class="dark-mode-toggle" id="darkModeToggle"><i class="fas fa-moon"></i></span>

    <!-- User Profile Dropdown -->
    <div class="profile-dropdown">
        <i class="fas fa-user-circle" style="font-size: 30px; color: white; cursor: pointer;"></i>
        <div class="dropdown-content">
            <a href="javascript:void(0)"><strong><?= htmlspecialchars($userData['username']); ?></strong></a>
            <a href="javascript:void(0)"><?= htmlspecialchars($userData['email']); ?></a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<div class="container">
    <h1>IT Management Courses</h1>
    
    <?php foreach ($courses as $course): ?>
        <div class="course-item">
            <h2><?= htmlspecialchars($course['course_name']); ?></h2>
            <p><?= htmlspecialchars($course['course_description']); ?></p>
            <!-- Enroll button -->
            <form method="POST">
                <input type="hidden" name="course_id" value="<?= $course['course_id']; ?>">
                <button type="submit">Enroll</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>

<!-- Profile Modal -->
<div id="profileModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" id="closeProfileModal">&times;</span>
        <h2>User Profile</h2>
        <p><strong>Profile ID:</strong> <?= htmlspecialchars($userData['user_id']); ?></p>
        <p><strong>Profile Name:</strong> <?= htmlspecialchars($userData['username']); ?></p>
        <p><strong>Date of Birth:</strong> <?= htmlspecialchars($userData['dob']); ?></p>
        <p><strong>Contact Number:</strong> <?= htmlspecialchars($userData['contact_number']); ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($userData['email']); ?></p>
    </div>
</div>

<script>
    // Sidebar toggle
    var sidebar = document.getElementById('sidebar');
    var sidebarToggle = document.getElementById('sidebarToggle');
    sidebarToggle.onclick = function() {
        if (sidebar.style.left === "0px") {
            sidebar.style.left = "-240px";
        } else {
            sidebar.style.left = "0px";
        }
    }

    // Dark Mode Toggle
    var darkModeToggle = document.getElementById('darkModeToggle');
    darkModeToggle.onclick = function() {
        document.body.classList.toggle('dark-mode');
    }

    // Search Courses
    var searchInput = document.getElementById('courseSearch');
    searchInput.addEventListener('input', function() {
        var filter = searchInput.value.toLowerCase();
        var courses = document.querySelectorAll('.course-item');
        courses.forEach(function(course) {
            var courseName = course.querySelector('h2').textContent.toLowerCase();
            if (courseName.indexOf(filter) > -1) {
                course.style.display = '';
            } else {
                course.style.display = 'none';
            }
        });
    });
</script>

</body>
</html>
