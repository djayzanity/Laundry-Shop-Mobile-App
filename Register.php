<?php
// Start the session to store messages
session_start();

// Include the database connection file
require 'connection.php';
// Initialize variables for messages
$success_message = "";
$error_message = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form inputs
    $fullname = trim($_POST['full_name']);
    $email = trim($_POST['email']);  // Email for the user
    $contact_number = trim($_POST['contact_number']);
    $_address = trim($_POST['_address']);
    $password = $_POST['pass'];  // User password

    // Basic validation (you can add more as needed)
    if (empty($fullname) || empty($email) || empty($contact_number) || empty($_address) || empty($password)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } elseif (!preg_match("/^[0-9]{10,15}$/", $contact_number)) {
        $error_message = "Invalid phone number format.";
    } else {
        // Check if email already exists using PDO
        $stmt = $pdo->prepare("SELECT id FROM user WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $error_message = "An account with this email already exists.";
        } else {
            // Hash the password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert user data into the database using prepared statements
            $stmt = $pdo->prepare("INSERT INTO user (full_name, email, contact_number, _address, pass) VALUES (:full_name, :email, :contact_number, :_address, :pass)");
            $stmt->bindParam(':full_name', $fullname);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':contact_number', $contact_number);
            $stmt->bindParam(':_address', $_address);
            $stmt->bindParam(':pass', $hashed_password);

            if ($stmt->execute()) {
                // Set success message to be passed to JavaScript
                $success_message = "Registration successful! You can now log in.";
                
                // Clear POST data
                $_POST = array();
            } else {
                $error_message = "Error registering user.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/css/Registercss.css">
    <title>Register</title>
</head>
<body> <style>
     

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
  min-height: 100vh;
  margin: 0;
  transition: opacity 0.5s ease-in-out;
    opacity: 1;

    /* Background overlay with image */
    background: 
       linear-gradient(rgba(0, 0, 0, 0.1), rgba(0, 0, 0, 0.9)), /* Light gray transparent overlay */
        url('/img/laundry.jpeg'); /* Background image */

        background-size: cover; /* Ensures the image covers the entire screen */
  background-position: center; /* Centers the image within the screen */
  background-repeat: no-repeat;
  width: 100vw; /* Sets body width to viewport width */
  height: 100vh; /* Sets body height to viewport height */
  overflow: hidden; /* Prevents scroll if image overflows */
}

body.fade-out {
  opacity: 0;
}

   /* Modal container */
   .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        /* Modal content */
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 400px;
            border-radius: 8px;
            text-align: center;
        }

        /* Close button */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
        }

        /* Success message styling */
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }

        /* Error message styling */
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }



/* Container for the login form */
.login-container {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  width: 100%;
  max-width: 400px;
  padding: 20px;
  box-sizing: border-box; 
}


.login-container img {
  width: 85%; 
  position: relative;
  top: -20px;
  height: auto; 
  object-fit: contain;
  animation: bounce 1s infinite alternate; /* Logo bounce animation */
  
}

.background {
  width: 400px;
  height: 500px;
}

/* Input box container */
.input-box {
  position: relative;
  width: 100%; /* Make it responsive */
  margin-bottom: 35px;
}

/* Input fields styling */
input {
  position: relative;
  left: -28px;
  width: 130%; /* Adjust input width to fit container */
  outline: none;
  font-size: 1rem; /* Responsive font size */
  padding: 15px;
  margin: -5px;
  border-radius: 10px;
  border: 1.5px solid white; /* Default border color */
  background: transparent;
  transition: 0.3s ease; /* Smooth transition for border change */
}

/* Label styles for all input fields */
.labelemail, .labelpass, .labelphone, .labeladdress, .labelname {
  position: absolute;
  left: -15px;
  top: 15px;
  font-size: 0.8rem;
  color: white;
  transition: 0.3s ease;
}

/* Focused input and valid state */
input:focus,
input:valid {
  border-color: #FF0000;
  color: white;
  font-size: 18px;
}

/* When input is focused or valid, adjust label */
input:focus + .labelname,
input:valid + .labelname,
input:focus + .labelemail,
input:valid + .labelemail,
input:focus + .labelphone,
input:valid + .labelphone,
input:focus + .labeladdress,
input:valid + .labeladdress,
input:focus + .labelpass,
input:valid + .labelpass {
  transform: translate(-18px, -38px) scale(0.8);
  color: #FF0000;
  font-weight: bold;
}

/* Terms and conditions container */
.term-container {
  width: 100%;
  display: flex;
  align-items: center;
  margin-top: 10px;
}

input[type="checkbox"] {
  height: 15px;
  width: 15px;
  margin-right: 10px;
  cursor: pointer;
}

.Label {
  font-size: 0.9rem;
}

.Span-term {
  color: red;
}

/* Button styling */
.btn button {
  display: inline-block;
  width: 120px;
  height: 30px;
  border-radius: 10px;
  border: 1px solid;
  position: relative;
  top: -8px;
  left: 38px;
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


/* Disabled button */
button:disabled {
  background-color: grey;
  cursor: not-allowed;
}

/* Button styling */
.back-btn button {
  display: inline-block;
  width: 120px;
  height: 30px;
  margin-top: 20px;
  border-radius: 10px;
  border: 1px solid;
  position: relative;
  top: -8px;
  left: 38px;
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



/* Media queries for smaller devices */
@media (max-width: 768px) {
  .login-container img{
      margin-left: 96px;
      margin-top: -70px;
     
    }


  .login-container {
    margin-left: -90px;
    max-width: 100%;
    padding: 10px;
  }

  input {
    position: relative;
    width: 142%;
    font-size: 0.8rem;
    padding: 20px 20px 20px 20px;
    margin-left: 20px;
    left: -17px;
  }

  input:focus,
input:valid {
  border-color: #FF0000;
  color: white;
  font-size: 15px;
}

  .labelemail, .labelpass, .labelphone, .labeladdress, .labelname {
    font-size: 15px;
    padding-left: 30px;
    left: -13px;
    margin-right: -24px;
  }

  .btn button {
    left: 33%;
    width: 70%;
    height: 5vh;
    
  }
}

@media (max-width: 480px) {

    .login-container img{
      margin-left: 96px;
      margin-top: -70px;
     
    }


  .login-container {
    margin-left: -90px;
    max-width: 100%;
    padding: 10px;
  }

  input {
    position: relative;
    width: 142%;
    font-size: 0.8rem;
    padding: 20px 20px 20px 20px;
    margin-left: 20px;
    left: -17px;
  }

  input:focus,
input:valid {
  border-color: #FF0000;
  color: white;
  font-size: 15px;
}

  .labelemail, .labelpass, .labelphone, .labeladdress, .labelname {
    font-size: 15px;
    padding-left: 30px;
    left: -13px;
    margin-right: -24px;
  }

  .btn button {
    left: 38%;
    width: 70%;
    height: 5vh;
    
  }

  
  .back-btn button {
    left: 48%;
    width: 50%;
    height: 3vh;
    
  }
}

        
    </style>
 <div class="login-container">
       
        <!-- Logo -->
        <img src="/img/logo new.png" alt="logo">
        <form id="registerForm" action="Register.php" method="POST">
            <div class="input-box">
                <input type="text" name="full_name" value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" required>
                <div class="labelname">Enter your Fullname</div>
            </div>

            <div class="input-box">
                <input type="email" name="email" value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" required>
                <div class="labelemail">Enter your Email</div>
            </div>

            <div class="input-box">
                <input type="text" name="contact_number" value="<?php echo isset($_POST['contact_number']) ? htmlspecialchars($_POST['contact_number']) : ''; ?>" required>
                <div class="labelphone">Enter your Phone number</div>
            </div>

            <div class="input-box">
                <input type="text" name="_address" value="<?php echo isset($_POST['_address']) ? htmlspecialchars($_POST['_address']) : ''; ?>" required>
                <div class="labeladdress">Enter your Address</div>
            </div>

            <div class="input-box">
                <input class="Pass" type="password" name="pass" required>
                <div class="labelpass">Create Password</div>
            </div>

         

            <div class="btn">
                <button type="submit" id="registerButton">Register</button>
            </div>

            <div class="back-btn">
                <button type="button" id="backButton" onclick="redirectToLogin()">Back</button>
            </div>
        </form>

        <!-- Success or Error Messages -->
        <div id="messageModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div id="modal-message"></div>
        </div>
    </div>

    <script>
        // Check if there are messages and display them in a modal
        <?php if (!empty($success_message)): ?>
            showMessage("<?php echo $success_message; ?>", 'success');
        <?php elseif (!empty($error_message)): ?>
            showMessage("<?php echo $error_message; ?>", 'error');
        <?php endif; ?>

        // Function to show the message in the modal
        function showMessage(message, type) {
            const modal = document.getElementById('messageModal');
            const messageContainer = document.getElementById('modal-message');
            
            // Set the message and style based on type
            messageContainer.innerHTML = message;
            if (type === 'success') {
                messageContainer.className = 'success-message';
                setTimeout(function() {
                    window.location.href = 'login.php';
                }, 2000); // Redirect after 3 seconds
            } else if (type === 'error') {
                messageContainer.className = 'error-message';
            }

            // Display the modal
            modal.style.display = "block";
        }

        // Close the modal when the user clicks the close button
        document.querySelector('.close').onclick = function() {
            document.getElementById('messageModal').style.display = "none";
        };

        // Close the modal if the user clicks outside of the modal content
        window.onclick = function(event) {
            const modal = document.getElementById('messageModal');
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };






        // Select the checkbox and submit button
const checkbox = document.getElementById('accept');
const submitBtn = document.getElementById('submit-btn');

// Add event listener to the checkbox
checkbox.addEventListener('change', function() {
    // Enable the submit button if the checkbox is checked
    submitBtn.disabled = !checkbox.checked;
});

function redirectToLogin() {
    window.location.href = 'login.php';
  }

    </script>

        <script src="/js/regis.js"></script>
    </div>
</body>
</html>
