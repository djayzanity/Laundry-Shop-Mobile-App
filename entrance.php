<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laundry Shop Management System</title>
  <style>
    /* General Styles */
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background-color: white;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      overflow: hidden;
    }

    #animation-wrapper {
      text-align: center;
    }

    /* Logo Container */
    .logo-container {
      position: relative;
      animation: fadeIn 2s ease-out forwards;
    }

    .circle {
      width: 400px;
      height: 400px;
      border-radius: 50%;
      margin: -120px auto;
      opacity: 0;
      transform: scale(0);
      animation: popIn 1.5s ease-out forwards;
      display: flex;
      justify-content: center;
      align-items: center;
      overflow: hidden; /* Ensures the image stays within the circle */
    }

    .circle img {
      width: 80%; /* Adjust the size of the image inside the circle */
      height: 50%;
      border-radius: 50%; /* Makes the image circular */
      background:transparent;
    }

    .shop-name {
      font-size: 2em;
      font-weight: bold;
      font-family:Arial, Helvetica, sans-serif;
      color: gray;
      top: -100px;
      margin: 40px 0 0;
      opacity: 0;
      animation: slideUp 1.5s ease-out 0.5s forwards;
    }

    .tagline {
      font-size: 1.2em;
      color: red;
      margin: 10px 0 0;
      opacity: 0;
      animation: fadeIn 1s ease-out 1s forwards;
    }

    /* Animations */
    @keyframes fadeIn {
      from {
        opacity: 0;
      }
      to {
        opacity: 1;
      }
    }

    @keyframes popIn {
      0% {
        opacity: 0;
        transform: scale(0);
      }
      100% {
        opacity: 1;
        transform: scale(1);
      }
    }

    @keyframes slideUp {
      0% {
        opacity: 0;
        transform: translateY(20px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>
  <div id="animation-wrapper">
    <div class="logo-container">
      <div class="circle">
        <img src="/img/logocircle.png" alt="Laundry Shop Logo">
      </div>
      <h1 class="shop-name">EnQ Laundry shop</h1>
    </div>
    <p class="tagline">Quick, easy, and efficient</p>
  </div>
  <script>
    // Redirect to login.php after the animation completes
    setTimeout(() => {
      window.location.href = 'login.php';
    }, 2000); // Adjust the timeout duration as needed
  </script>
</body>
</html>
