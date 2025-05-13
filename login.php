<?php
// Start the session
session_start();

// Include the database connection file
require 'connection.php'; // Ensure this points to your correct connection file


// Initialize variables for messages
$success_message = "";
$error_message = "";

// Handle form submission for login
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form inputs
    $email = trim($_POST['email']);
    $password_input = ($_POST['pass']); // Matching the form input name

    // Basic validation
    if (empty($email) || empty($password_input)) {
        $error_message = "Both email and password are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } else {
        // Prepare a statement using PDO
        $stmt = $pdo->prepare("SELECT id, full_name, pass FROM user WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Fetch the user data
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $user_id = $user['id'];
            $full_name = $user['full_name'];
            $hash_password = $user['pass'];  // This retrieves the hashed password from the database

            // Verify the password
            if (password_verify($password_input, $hash_password)) {
                // Set session variables
                $_SESSION['user_email'] = $email;
                $_SESSION['user_fullname'] = $full_name;
                $_SESSION['user_id'] = $user_id;
                $_SESSION['userpass'] = $password_input;

                // Success message and redirect to the user dashboard
              // Redirect to the user dashboard
              header("Location: userdashboard.php");
              exit();
          } else {
              $password_error_message = "Incorrect password!";
          }
      } else {
          $email_error_message = "Email not found!";
      }
  }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Link to your CSS file -->
    <link rel="stylesheet" href="/css/Logincss.css">

    <style>
@import url('https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap');

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
  font-family: 'Raleway';
}
body{
  display: flex;
  justify-content: center;
  align-items: center;
  min-height: 90vh;

    /* Background overlay with image */
    background: 
        linear-gradient(rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.9)), /* Light gray transparent overlay */
        url('/img/laundry.jpeg'); /* Background image */

    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    height: 100vh; /* Ensures the background covers the full viewport height */

}



.login-container img{
  width: 90%; /* Makes the image full width of its container */
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: flex-start;
  position: relative;
  left: 7px;
  
}
.login-container{
  display: flex;
  flex-direction: column; /* Stacks items vertically */
  justify-content: center;
  align-items: center;/* Pushes items to the top */
  width: 100%;
  max-width: 400px; 
}

.input-box{
  position: relative;
  height: 60px;
  line-height: 90px;
  margin-bottom: 40px;
  
}
input {
  position: absolute; 
  width: 132%;
  outline: none;
  font-size: 0.9em; /* Normal font size for input text */
  padding: 17px 14px; /* Adjust padding to fit the normal font size */
  line-height: normal;
  border-radius: 10px;
  border: 1.5px solid white;
  margin-top: 15%;
  top: -25px;
  left: -35px; 
  background: transparent;
  transition: 0.1s ease;
  z-index: 3;
  
}

.labelemail, .labelpass {
  position: absolute;
  left: -12px;
  top: -8px;
   font-size: 13px;
   color: white;
   transition: 0.5s ease; 
}


input:focus,
input:valid{
  color: white;
  border: 2px solid red;
  font-size: 18px;
}

input:focus + .labelemail,
input:valid + .labelemail,
input:focus + .labelpass,
input:valid + .labelpass{
  color: #FF0000;
  font-weight: bold;
  font-size: 15px;
  height: 34px;
  line-height: 5px;
  transform: translate(-20px, -10px) scale(0.80);
  z-index: 4;
}


.error-message {
  position: relative;
  top: -50px;
  left: 130px;
  color: #e74c3c;
  font-size: 14px;
  min-height: 20px; /* Ensures error message space is fixed */
  margin-top: 10px; /* Adjust margin for spacing */
}


/* From uiverse.io by @Ali-Tahmazi99 */
.btn button {
  display: inline-block;
  width: 120px;
  height: 30px;
  border-radius: 10px;
  border: 1px solid;
  position: relative;
  left: 44px;
  overflow: hidden;
  transition: all 0.5s ease-in;
  z-index: 1;
  color: black;
  font-weight: 500;
  cursor: pointer;
 }
 
 button::before,
 button::after {
  content: '';
  position: absolute;
  top: 0;
  width: 0;
  height: 100%;
  transform: skew(15deg);
  transition: all 0.5s;
  overflow: hidden;
  z-index: -1;
 }
 
 button::before {
  left: -9px;
  background: red;
  color: white;
 }
 
 button::after {
  right: -9px;
  background: red;
 }
 
 button:hover::before,
 button:hover::after {
  width: 58%;
 }
 
 button:hover span {
  color: #e0aaff;
  transition: 0.3s;
 }
 
 button span {
  color: #03045e;
  font-size: 18px;
  transition: all 0.3s ease-in;
 }

.register {
  margin-top: 50%;
  cursor: pointer;
}

.register a {
  color: red;
  font-size: 13px;
  text-decoration: underline;
  text-align: left
}
.register p {
  color:white;
}


.forgot-password {
    text-align: right;
    margin-top: -10px;
    margin-bottom: 20px;
    position: relative;
    font-size: 13px;
    top: -24px;
    left: 30px;
    
}

.forgot-password a {
    color: red;
    text-decoration: none;
}



    </style>

    
</head>
<body>
<div class="login-container">

    <!-- Logo -->
    <img src="img/logo new.png" alt="logo">

    <!-- Display success message if available -->
    <?php if (!empty($success_message)): ?>
        <div class="success-message"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <!-- Display error message if available -->
    <?php if (!empty($error_message)): ?>
        <div class="error-message"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <!-- Login Form -->
    <form id="loginForm" action="login.php" method="POST">

        <div class="input-box <?php echo !empty($email_error_message) ? 'error' : ''; ?>">
            <input type="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            <div class="labelemail">Enter your Email</div>
            <!-- Display email error message if available -->
            <?php if (!empty($email_error_message)): ?>
                <div class="error-message"><?php echo $email_error_message; ?></div>
            <?php endif; ?>
        </div>

        <div class="input-box <?php echo !empty($password_error_message) ? 'error' : ''; ?>">
            <input type="password" name="pass" required>
            <div class="labelpass">Enter your Password</div>
            <!-- Display password error message if available -->
            <?php if (!empty($password_error_message)): ?>
                <div class="error-message"><?php echo $password_error_message; ?></div>
            <?php endif; ?>
        </div>

        <!-- Forgot Password Link -->
       <!-- <div class="forgot-password">
            <a href="forgot.php">Forgot Password?</a>
        </div> -->

        <div class="btn">
            <!-- Submit button -->
            <button type="submit">Login</button>
        </div>

        <div class="register">
            <!-- Link to register page -->
            <p>Don’t have an account? <a href="Register.php">Register!</a></p>
        </div>

    </form>

</div>


            <script>
        // Client-side validation can also be added here
        document.getElementById('loginForm').addEventListener('submit', function (e) {
            const emailInput = document.querySelector('input[name="email"]');
            const passwordInput = document.querySelector('input[name="pass"]');
            
            // Clear previous error messages
            clearErrorMessages();

            // Validate email format
            if (!emailInput.value.includes('@')) {
                displayError(emailInput, 'Please enter a valid email.');
                e.preventDefault(); // Prevent form submission
            }

            // Check if password is empty
            if (passwordInput.value.trim() === '') {
                displayError(passwordInput, 'Password is required.');
                e.preventDefault();
            }
        });

        // Function to clear error messages
        function clearErrorMessages() {
            const errorMessages = document.querySelectorAll('.error-message');
            errorMessages.forEach(function (message) {
                message.remove();
            });

            const inputs = document.querySelectorAll('.input-box');
            inputs.forEach(function (input) {
                input.classList.remove('error');
            });
        }

        // Function to display error messages next to inputs
        function displayError(inputElement, message) {
            const errorDiv = document.createElement('div');
            errorDiv.classList.add('error-message');
            errorDiv.textContent = message;
            inputElement.parentElement.appendChild(errorDiv);
            inputElement.parentElement.classList.add('error');
        }
    </script>

</body>
</html>
