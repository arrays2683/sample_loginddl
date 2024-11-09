<?php
session_start();
require 'db.connection.php';

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        header("Location: landingpage.php");
        exit();
    } else {
        $error_message = "Invalid credentials";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <style>
        body {
            background-color: #000;
            overflow: hidden; /* Prevent overflow during animation */
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        .card {
            position: relative; /* Allow absolute positioning of text container */
        }

        .img-container {
            position: relative;
            overflow: hidden; /* Ensure the image doesn't overflow when scaled */
            transition: transform 0.8s ease; /* Image scaling effect */
        }

        .img-container img {
            object-fit: cover;
            width: 100%;
            height: auto; /* Maintain the ratio */
            transform: scale(1.1); /* Slightly scale the image */
            transition: transform 0.8s ease; /* Animate scaling */
        }

        .text-container {
            position: absolute; /* Position text absolutely within the card */
            top: 50%; /* Center text vertically */
            left: 10%; /* Align text to the left */
            transform: translateY(-50%); /* Adjust for centering */
            color: white;
            text-align: left; /* Left align the text */
            opacity: 0; /* Start invisible for fade in */
            animation: fadeInUp 0.8s forwards; /* Animation */
        }

        .text-container h1 {
            margin: 0; /* Remove default margins */
            font-size: 150%;
            font-weight: 100;
        }

        .highlight-green {
            color: green;
        }

        .highlight-blue {
            color: blue;
            margin-top: 5px; /* Space it slightly below the first line */
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px); /* Start from below */
            }
            to {
                opacity: 1;
                transform: translateY(0); /* Move to original position */
            }
        }

        @media (max-width: 768px) {
            .text-container {
                left: 5%; /* Adjust positioning for smaller screens */
            }
        }
    </style>
</head>

<body>
    <section class="vh-100">
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col col-xl-10">
                    <div class="card">
                        <div class="row g-0">
                            <div class="col-md-6 img-container">
                                <img src="Image/CCS_Design.png" alt="Login Image" class="img-fluid">
                                <div class="text-container">
                                    <h1 class="highlight-green">"Empowering Minds,"</h1> <!-- First line on the left -->
                                    <h1 class="highlight-blue">"Transforming Technology"</h1> <!-- Second line directly beneath the first line -->
                                </div>
                            </div>
                            <div class="col-md-6 col-lg-5 d-flex align-items-center">
                                <div class="card-body p-4 p-lg-5">
                                    <h2 class="text-center">Login</h2>
                                    <form method="POST" action="index.php">
                                        <?php if ($error_message): ?>
                                            <div class="error">
                                                <?php echo htmlspecialchars($error_message); ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="form-outline mb-3">
                                            <input type="text" name="username" class="form-control form-control-lg" required />
                                            <label class="form-label">Username</label>
                                        </div>
                                        <div class="form-outline mb-3">
                                            <input type="password" name="password" class="form-control form-control-lg" required />
                                            <label class="form-label">Password</label>
                                        </div>
                                        <div class="pt-1 mb-4">
                                            <button class="btn btn-dark btn-lg btn-block" type="submit">Login</button>
                                        </div>
                                        <p class="mb-5">
                                            Don't have an account? <a href="register.php" style="color: #007bff;">Register here</a>
                                        </p>
                                        <a href="#" class="small text-muted">Terms of use. Privacy policy</a>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.js"></script>
    <script>
        // Scale the image back to normal when the page loads
        window.onload = function() {
            const imgContainer = document.querySelector('.img-container img');
            imgContainer.style.transform = 'scale(1)'; // Reset to normal scale
        };
    </script>
</body>

</html>