<?php
session_start();
require 'db.connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user profile details
$stmt = $pdo->prepare("SELECT u.username, u.email, p.full_name, p.date_of_birth, p.contact_number, u.user_id AS profile_id
                       FROM users u
                       LEFT JOIN profiles p ON u.user_id = p.user_id
                       WHERE u.user_id = ?");
$stmt->execute([$user_id]);
$userData = $stmt->fetch();

// Handle Profile Update
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['full_name'];
    $date_of_birth = $_POST['date_of_birth'];
    $contact_number = $_POST['contact_number'];

    $stmt = $pdo->prepare("UPDATE profiles SET full_name = ?, date_of_birth = ?, contact_number = ? WHERE user_id = ?");
    $stmt->execute([$full_name, $date_of_birth, $contact_number, $user_id]);
    echo "<p>Profile updated successfully!</p>";
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.css" />
    <style>
        /* Global Styles */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #2a3d66, #1e2a47);
            background-size: 200% 200%;
            animation: backgroundAnimation 15s ease infinite;
            color: #fff;
        }

        @keyframes backgroundAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Navbar Styling */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: #1e2a47;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 100;
        }

        nav a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }

        nav a:hover {
            background-color: #3a4c79;
        }

        .profile-dropdown {
            position: relative;
            display: inline-block;
        }

        .profile-dropdown:hover .dropdown-menu {
            display: block;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            right: 0;
            background-color: #333;
            min-width: 180px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            z-index: 1;
        }

        .dropdown-menu a {
            color: white;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            transition: background-color 0.3s ease;
        }

        .dropdown-menu a:hover {
            background-color: #575757;
        }

        /* Sidebar Styles */
        #rightSidebar {
            position: fixed;
            top: 0;
            right: -240px;
            width: 240px;
            height: 100%;
            background-color: #333;
            transition: right 0.3s ease;
            z-index: 999;
            color: white;
        }

        #rightSidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        #rightSidebar ul li {
            padding: 20px;
            border-bottom: 1px solid #444;
        }

        #rightSidebar ul li a {
            color: white;
            text-decoration: none;
        }

        #rightSidebar ul li a:hover {
            background-color: #575757;
        }

        #toggleSidebarBtn {
            font-size: 24px;
            color: white;
            cursor: pointer;
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1100;
        }

        /* Table Styling */
        table {
            width: 80%;
            margin: 30px auto;
            border-collapse: collapse;
            background-color: #222;
            border-radius: 8px;
        }

        table th, table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #444;
        }

        table th {
            background-color: #3a4c79;
        }

        table tr:hover {
            background-color: #444;
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

<!-- Navbar -->
<nav>
    <a href="dashboard.php">Dashboard</a>
    <a href="landingpage.php">Landing Page</a>
    <a href="enroll.php">Enrollment</a>
    <a href="courses.php">Courses</a>
    <a href="feedback.php">Feedback</a>

    <!-- Profile Dropdown -->
    <div class="profile-dropdown">
        <i class="fas fa-user-circle" style="font-size: 30px; color: white; cursor: pointer;"></i>
        <div class="dropdown-menu">
            <a href="#" id="viewProfile">Student Profile</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<!-- Right Sidebar -->
<div id="rightSidebar">
    <ul>
        <li><a href="#">Dashboard</a></li>
        <li><a href="#">User Profile</a></li>
        <li><a href="#">Courses</a></li>
        <li><a href="#">Enroll</a></li>
        <li><a href="#">Feedback</a></li>
    </ul>
</div>

<!-- Toggle Sidebar Button -->
<i id="toggleSidebarBtn" class="fas fa-arrow-left"></i>

<!-- Profile Table -->
<div class="container">
    <h1>Student Profile</h1>
    <table>
        <tr>
            <th>Profile ID</th>
            <td><?= htmlspecialchars($userData['profile_id']); ?></td>
        </tr>
        <tr>
            <th>Full Name</th>
            <td><?= htmlspecialchars($userData['full_name']); ?></td>
        </tr>
        <tr>
            <th>Date of Birth</th>
            <td><?= htmlspecialchars($userData['date_of_birth']); ?></td>
        </tr>
        <tr>
            <th>Contact Number</th>
            <td><?= htmlspecialchars($userData['contact_number']); ?></td>
        </tr>
    </table>

    <!-- Profile Update Form -->
    <h2>Update Your Profile</h2>
    <form method="POST">
        <label>Full Name</label>
        <input type="text" name="full_name" value="<?= htmlspecialchars($userData['full_name']); ?>" required>

        <label>Date of Birth</label>
        <input type="date" name="date_of_birth" value="<?= htmlspecialchars($userData['date_of_birth']); ?>" required>

        <label>Contact Number</label>
        <input type="text" name="contact_number" value="<?= htmlspecialchars($userData['contact_number']); ?>" required>

        <button type="submit">Update Profile</button>
    </form>
</div>

<!-- Profile Modal -->
<div id="profileModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" id="closeProfileModal">&times;</span>
        <h2>User Profile</h2>
        <p><strong>Profile ID:</strong> <?= htmlspecialchars($userData['profile_id']); ?></p>
        <p><strong>Full Name:</strong> <?= htmlspecialchars($userData['full_name']); ?></p>
        <p><strong>Date of Birth:</strong> <?= htmlspecialchars($userData['date_of_birth']); ?></p>
        <p><strong>Contact Number:</strong> <?= htmlspecialchars($userData['contact_number']); ?></p>
    </div>
</div>

<script>
    // Toggle Sidebar
    var sidebar = document.getElementById('rightSidebar');
    var toggleSidebarBtn = document.getElementById('toggleSidebarBtn');
    toggleSidebarBtn.onclick = function() {
        if (sidebar.style.right === "0px") {
            sidebar.style.right = "-240px";
        } else {
            sidebar.style.right = "0px";
        }
    }

    // Open profile modal
    var viewProfileLink = document.getElementById('viewProfile');
    var profileModal = document.getElementById('profileModal');
    var closeProfileModal = document.getElementById('closeProfileModal');
    
    viewProfileLink.onclick = function() {
        profileModal.style.display = 'block';
    }
    
    closeProfileModal.onclick = function() {
        profileModal.style.display = 'none';
    }

    // Close modal if clicked outside
    window.onclick = function(event) {
        if (event.target === profileModal) {
            profileModal.style.display = 'none';
        }
    }
</script>

</body>
</html>
