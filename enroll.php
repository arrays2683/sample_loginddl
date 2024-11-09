<?php
session_start();
require 'db.connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user profile details (Full name, Email)
$stmt = $pdo->prepare("SELECT u.username, u.email, p.full_name 
                       FROM users u
                       LEFT JOIN profiles p ON u.user_id = p.user_id
                       WHERE u.user_id = ?");
$stmt->execute([$user_id]);
$userData = $stmt->fetch();

// Handle Add/Edit/Delete Profile
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['edit_profile'])) {
        // Edit Profile
        $full_name = $_POST['full_name'];
        $stmt = $pdo->prepare("UPDATE profiles SET full_name = ? WHERE user_id = ?");
        $stmt->execute([$full_name, $user_id]);
        echo "<script>alert('Profile updated successfully!');</script>";
    }

    if (isset($_POST['delete_profile'])) {
        // Delete Profile
        $stmt = $pdo->prepare("DELETE FROM profiles WHERE user_id = ?");
        $stmt->execute([$user_id]);
        session_destroy();
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.css" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #0f4b5c, #0e72d7);
            background-size: 200% 200%;
            animation: backgroundAnimate 15s ease infinite;
            color: white;
        }

        @keyframes backgroundAnimate {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            margin: 20px auto;
            text-align: center;
            width: 80%;
        }

        .btn {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn:hover {
            background-color: #218838;
        }

        /* Profile Icon on Left */
        #profileIcon {
            position: fixed;
            top: 20px;
            left: 20px;
            background-color: #0e72d7;
            border-radius: 50%;
            padding: 15px;
            color: white;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.3);
        }

        #profileIcon:hover {
            background-color: #2e3d5d;
        }

        /* Modal for Edit Profile */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease-out;
        }

        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            padding: 20px;
            width: 80%;
            max-width: 400px;
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

        /* Back Modal */
        .back-modal {
            display: none;
            position: fixed;
            z-index: 999;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .back-modal-content {
            background-color: #fff;
            padding: 20px;
            max-width: 400px;
            margin: 10% auto;
            border-radius: 10px;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>

<!-- Profile Icon -->
<div id="profileIcon">
    <i class="fas fa-user"></i>
</div>

<!-- Edit Profile Modal -->
<div id="editProfileModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" id="closeEditModal">&times;</span>
        <h2>Edit Profile</h2>
        <form method="POST">
            <label for="full_name">Full Name:</label>
            <input type="text" name="full_name" value="<?= htmlspecialchars($userData['full_name']); ?>" required>
            <br><br>
            <button type="submit" name="edit_profile" class="btn">Update Profile</button>
        </form>
    </div>
</div>

<!-- Delete Profile Modal -->
<div id="deleteProfileModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" id="closeDeleteModal">&times;</span>
        <h2>Are you sure you want to delete your profile?</h2>
        <form method="POST">
            <button type="submit" name="delete_profile" class="btn">Yes, Delete Profile</button>
            <button type="button" class="btn" id="cancelDelete">Cancel</button>
        </form>
    </div>
</div>

<!-- Back Confirmation Modal -->
<div id="backModal" class="back-modal">
    <div class="back-modal-content">
        <h2>Are you sure you want to go back?</h2>
        <button id="goBackBtn" class="btn">Go Back</button>
        <button id="cancelBackBtn" class="btn">Cancel</button>
    </div>
</div>

<!-- Main Content -->
<div class="container">
    <h1>Your Profile Information</h1>
    <p><strong>Full Name:</strong> <?= htmlspecialchars($userData['full_name']); ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($userData['email']); ?></p>

    <!-- Buttons for Edit, Delete -->
    <button id="editProfileBtn" class="btn">Edit Profile</button>
    <button id="deleteProfileBtn" class="btn" style="background-color: #dc3545;">Delete Profile</button>
    <button id="backBtn" class="btn" style="background-color: #007bff;">Back</button>
</div>

<script>
    // Profile Icon Modal
    var profileIcon = document.getElementById('profileIcon');
    var editProfileModal = document.getElementById('editProfileModal');
    var deleteProfileModal = document.getElementById('deleteProfileModal');
    var backModal = document.getElementById('backModal');
    var closeEditModal = document.getElementById('closeEditModal');
    var closeDeleteModal = document.getElementById('closeDeleteModal');
    var cancelDelete = document.getElementById('cancelDelete');
    var goBackBtn = document.getElementById('goBackBtn');
    var cancelBackBtn = document.getElementById('cancelBackBtn');
    var editProfileBtn = document.getElementById('editProfileBtn');
    var deleteProfileBtn = document.getElementById('deleteProfileBtn');
    var backBtn = document.getElementById('backBtn');

    // Open edit profile modal
    editProfileBtn.onclick = function() {
        editProfileModal.style.display = 'block';
    }

    // Open delete profile modal
    deleteProfileBtn.onclick = function() {
        deleteProfileModal.style.display = 'block';
    }

    // Open back confirmation modal
    backBtn.onclick = function() {
        backModal.style.display = 'block';
    }

    // Close modals
    closeEditModal.onclick = function() {
        editProfileModal.style.display = 'none';
    }

    closeDeleteModal.onclick = function() {
        deleteProfileModal.style.display = 'none';
    }

    cancelDelete.onclick = function() {
        deleteProfileModal.style.display = 'none';
    }

    cancelBackBtn.onclick = function() {
        backModal.style.display = 'none';
    }

    // Confirm Back Action
    goBackBtn.onclick = function() {
        window.location.href = 'dashboard.php'; // Replace with your desired destination
    }

    // Close modal if clicked outside of it
    window.onclick = function(event) {
        if (event.target === editProfileModal || event.target === deleteProfileModal || event.target === backModal) {
            event.target.style.display = 'none';
        }
    }
</script>

</body>
</html>
