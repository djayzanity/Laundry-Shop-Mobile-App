<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User and Admin Login</title>
    <style>
        /* Basic reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Background and full-page styling */
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to right, #ff4e50, #ff6a7d); /* Red and pink gradient */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: white;
            text-align: center;
            margin: 0;
        }

        /* Container for the content */
        .container {
            background: white;
            color: #333;
            border-radius: 15px;
            padding: 40px 30px;
            width: 300px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }

        /* Title styling */
        h1 {
            font-size: 2.5rem;
            margin-bottom: 20px;
            font-weight: 700;
            color: #ff4e50; /* Red color */
        }


        /* Button container styling */
        .button-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* General button styles */
        .btn {
            padding: 15px;
            font-size: 1.1rem;
            text-decoration: none;
            text-transform: uppercase;
            font-weight: bold;
            color: white;
            border-radius: 5px;
            transition: all 0.3s ease;
            display: inline-block;
            width: 100%;
            text-align: center;
        }

        /* User button specific styles */
        .user-btn {
            background: #ff4e50; /* Red background for user */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Admin button specific styles */
        .admin-btn {
            background: #ff6a7d; /* Lighter red-pink background for admin */
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Button hover effects */
        .btn:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        .user-btn:hover {
            background: #ff7a8c; /* Lighter red on hover */
        }

        .admin-btn:hover {
            background: #ff8f99; /* Lighter pink on hover */
        }

        /* Mobile responsiveness */
        @media (max-width: 600px) {
            .container {
                width: 90%;
                padding: 30px;
            }

            h1 {
                font-size: 2rem;
            }

            .btn {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to Our App</h1>
        <div class="button-container">
            <a href="entrance.php" class="btn user-btn">User Login</a>
            <a href="admin_file/adminlogin.php" class="btn admin-btn">Admin Login</a>
        </div>
    </div>
</body>
</html>
