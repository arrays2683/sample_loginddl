<?php
require 'db.connection.php';

$error_message = ''; // Initialize error message

// Check for error message in the URL
if (isset($_GET['error'])) {
    $error_message = $_GET['error'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- (Your existing code here) -->
</head>
<body>
<!-- Section: Design Block -->
<section>
  <!-- (Your existing code here) -->
  <div class="text-center mt-2">
    <!-- Error message -->
    <?php if ($error_message): ?>
      <div class="alert alert-danger" role="alert" style="font-size: 0.9rem;">
        <?php echo htmlspecialchars($error_message); ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- MDB UI Kit JavaScript -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.js"></script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
    <style>
        body {
            background-color: #1a1a1a; /* Light black background */
            color: #fff; /* White text color */
        }
        .highlight {
            color: hsl(217, 10%, 50.8%);
            background-color: #ffeb3b; /* Yellow highlight */
            font-weight: bold;
            display: inline-block;
            padding: 5px 10px;
            margin-top: 10px; /* Added margin for alignment */
        }
        .card {
            background-color: #fff; /* White background for the card */
            color: #333; /* Dark text color inside the card */
            border: 1px solid #444; /* Light border for better visibility */
            border-radius: 8px; /* Slightly rounded corners */
        }
        .form-label {
            color: #333; /* Dark grey text color for labels */
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
        .btn-social {
            background-color: #fff; /* White background for better contrast */
            border-radius: 50%;
            font-size: 18px;
            margin: 0 5px;
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #ccc; /* Light border */
            color: #333; /* Dark text color for better visibility */
        }
        .btn-facebook { background-color: #3b5998; color: #fff; }
        .btn-google { background-color: #db4437; color: #fff; }
        .btn-twitter { background-color: #1da1f2; color: #fff; }
        .btn-github { background-color: #333; color: #fff; }
    </style>
</head>
<body>

<!-- Section: Design Block -->
<section>
  <div class="px-4 py-5 px-md-5 text-center text-lg-start" style="background-color: #2a2a2a;"> 
    <div class="container">
      <div class="row gx-lg-5 align-items-center">
        <div class="col-lg-6 mb-5 mb-lg-0">
          <h1 class="my-5 display-3 fw-bold ls-tight">
            NBSC <br />
            <span class="text-primary">College of Computer Studies</span>
          </h1>
          <?php echo '<p class="highlight"> "Empowering Minds, Transforming Technology" </p>'; ?>
        </div>

        <div class="col-lg-6 mb-5 mb-lg-0 d-flex justify-content-center">
          <div class="card p-4 w-100"> <!-- Width added to card for better responsiveness -->
            <div class="card-body">
              <form method="POST" action="register_process.php">
              
                <div class="row">
                  <div class="col-md-6 mb-4">
                    <div data-mdb-input-init class="form-outline">
                      <input type="text" id="form3Example1" name="firstname" class="form-control" />
                      <label class="form-label" for="form3Example1">First name</label>
                    </div>
                  </div>

                  <div class="col-md-6 mb-4">
                    <div data-mdb-input-init class="form-outline">
                      <input type="text" id="form3Example2" name="lastname" class="form-control" />
                      <label class="form-label" for="form3Example2">Last name</label>
                    </div>
                  </div>
                </div>

                <div class="mb-4">
                  <div data-mdb-input-init class="form-outline">
                    <input type="text" id="form3username" name="username" class="form-control" />
                    <label class="form-label" for="form3username">Username</label>
                  </div>
                </div>

                <!-- Email input -->
                <div class="mb-4">
                  <div data-mdb-input-init class="form-outline">
                    <input type="email" id="form3Example3" name="email" class="form-control" />
                    <label class="form-label" for="form3Example3">Email address</label>
                  </div>
                </div>

                <!-- Password input -->
                <div class="mb-4">
                  <div data-mdb-input-init class="form-outline">
                    <input type="password" id="form3Example4" name="password" class="form-control" />
                    <label class="form-label" for="form3Example4">Password</label>
                  </div>
                </div>

                <!-- Checkbox -->
                <div class="form-check mb-4">
                  <input class="form-check-input" type="checkbox" id="form2Example33" />
                  <label class="form-check-label" for="form2Example33">
                    Subscribe to our newsletter
                  </label>
                </div>

                <!-- Error message -->
                <?php if (isset($error_message)): ?>
                  <div class="alert alert-danger" role="alert" style="font-size: 0.9rem;">
                    <?php echo $error_message; ?>
                  </div>
                <?php endif; ?>

                <!-- Submit button -->
                <button type="submit" class="btn btn-primary btn-block mb-4">
                  Register
                </button>

                <!-- Register buttons -->
                <div class="text-center mt-2">
                  <p>or sign up with:</p>
                  <button type="button" class="btn btn-social btn-facebook">
                    <i class="fab fa-facebook-f"></i>
                  </button>

                  <button type="button" class="btn btn-social btn-google">
                    <i class="fab fa-google"></i>
                  </button>

                  <button type="button" class="btn btn-social btn-twitter">
                    <i class="fab fa-twitter"></i>
                  </button>

                  <button type="button" class="btn btn-social btn-github">
                    <i class="fab fa-github"></i>
                  </button>
                </div>

                <!-- Login link -->
                <div class="text-center mt-2">
                  <p>Already have an account? <a href="index.php">Login</a></p>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- MDB UI Kit JavaScript -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/6.3.0/mdb.min.js"></script>
</body>
</html>
