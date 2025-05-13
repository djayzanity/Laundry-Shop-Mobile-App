<?php
// Assuming your database connection is already established and available in this scope as $pdo

include "connection.php";
// Execute the query to count unique users from the past 30 days
$stmt = $pdo->query("
    SELECT COUNT(DISTINCT user_id) AS unique_user_count
    FROM _statement
    WHERE created_at >= NOW() - INTERVAL 30 DAY
");
$userCount = $stmt->fetchColumn();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="Front.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
        <title>E&Q Laundry Shop</title>
    </head>
    <body>
        <header>
            <div class="container">
                <div class="logo">
                    <img src="img/logo new.png" alt=""></a>
                </div>
                <nav class="nav-menu">
                    <ul>
                        <li><a href="#back">Home</a></li>
                        <li><a href="#us">About us</a></li>
                        <li><a href="#services">Services</a></li>
                        <li><a href="#package">Package</a></li>
                        <li><a href="https://drive.google.com/drive/folders/15zeGlpB0UxYcKNYltfwed5Ybn28TnMih" target="_blank">Download</a></li>
                    </ul>
                </nav>
            </div>
        </header>
        <!-- HOME SECTION -->
        <section class="home" id="back">
                <div class="overlay"> </div>
                <div class="txt-overlay">
                <p>

                    <mark class="mark"> Fast, simple, and efficient</mark>—our self-service laundry shop makes laundry day a breeze. Wash, dry, and go at your convenience.
                </p>
             </div>
            </section>

             <section>
                <div class="contact-box">
                    <div class="contact-item">
                        <strong><mark class="mark">PHONE:</mark></strong>   <span>+63 9123 456 7891</span>
                    </div>
                    <div class="contact-item">
                        <strong><mark class="mark">OPEN HOURS:</mark></strong> <span>Weekdays: 7 AM - 5 PM <br> Weekend 7 AM - 7 PM</span>
                    </div>
                    <div class="contact-item">  
                        <strong><mark class="mark">EMAIL:</mark></strong>  <span>info@example.com</span>
                        </div>
                </div>
        </section>

        <!-- ABOUT SECTION -->
        <section id="us">
            <div class="custom-shape-divider-top-1724909802">
                <svg data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 120" preserveAspectRatio="none">
                    <path d="M321.39,56.44c58-10.79,114.16-30.13,172-41.86,82.39-16.72,168.19-17.73,250.45-.39C823.78,31,906.67,72,985.66,92.83c70.05,18.48,146.53,26.09,214.34,3V0H0V27.35A600.21,600.21,0,0,0,321.39,56.44Z" class="shape-fill"></path>
                </svg>
            </div>

            <div class="box" >
                <h1>Our Mission</h1>
                <div class="row">
                    <div class="About">
                        <img src="img/mission.png" alt="mission">
                        <h2>MISSION STATEMENT</h2>
                        <p>
                            To provide our community with affordable, clean & easy way of doing their laundry chores.
                        </p>
                        </div>
                        <div class="About">
                            <img src="img/vision.png" alt="vision">
                            <h2>VISION STATEMENT</h2>
                            <p>
                                To become the reliable and most preferred self-service laundry shop of the community
                            </p>
                        </div>
                            <div class="About">
                                <img src="img/core.png" alt="core">
                                <h2>CORE VALUE</h2>
                                <p>
                                    Take care of customers first, 
                                    Take care of each other, 
                                    Be responsible & do the right things
                                    Keep moving forward, 
                                    Have fun along the way
                                </p>
                            </div>

                    </div>
                </div> 
            
           

        </section>

<!-- SERVICES SECTION -->
<section id="services">
    <div class="services-container">
        <img src="img/quality.png" alt="quality" class="serimg">
        <h1>Our Services</h1>
        <div class="service-grid">
            <div class="service-item">
                <img src="img/laundry-machine.png" alt="Wash Icon">
                <p>WASH</p>
            </div>
            <div class="service-item">
                <img src="img/dryer.png" alt="Dry Icon">
                <p>DRY</p>
            </div>
            <div class="service-item">
                <img src="img/delivery-man.png" alt="Pickup and Delivery Icon">
                <p>PICK UP AND DELIVERY</p>
            </div>
            <div class="service-item">
                <img src="img/ironing.png" alt="Ironing Icon">
                <p>IRONING</p>
            </div>
            <div class="service-item">
                <img src="img/stain-remover.png" alt="Stain Removal Icon">
                <p>STAIN REMOVAL</p>
            </div>
        </div>
    </div>
</section>


            <!-- PACKAGE SECTION  -->
          <section id="package" class="packages-section">
            <h1>Our Package</h1>
            <div class="packages-container">
                <!-- Standard Package -->
                <div class="package-box">
                    <div class="package-icon">
                        <img src="img/basic.jpg" alt="Basic">
                    </div>
                    <h2 class="package-title">Basic Promo</h2>
                    <p class="package-time">Convenient, everyday laundry solution for quick care.</p>
                    <p class="package-price">₱150</p>
                    <ul class="package-features">
                        <li>Light Wash</li>
                        <li>Dry</li>
                        <li>Detergent</li>
                        <li>Fabric Softener</li>
                    </ul>
                </div>
    
                <!-- Super Package -->
                <div class="package-box">
                    <div class="package-icon">
                        <img src="img/deluxe.jpg" alt="Super package">
                    </div>
                    <h2 class="package-title">Deluxe Promo</h2>
                    <p class="package-time">Enhanced laundry care with added attention to detail.</p>
                    <p class="package-price">₱220</p>
                    <ul class="package-features">
                        <li>Fold</li>
                        <li>Fabric Softener</li>
                        <li>Detergent</li>
                        <li>Dry</li>
                        <li>Mixed Wash</li>
                    </ul>
                </div>
                <div class="package-box">
                    <div class="package-icon">
                        <img src="img/premium.jpg" alt="Premium Promo">
                    </div>
                    <h2 class="package-title">Premium Promo</h2>
                    <p class="package-time">Full-service laundry experience for pristine results.</p>
                    <p class="package-price">250</p>
                    <ul class="package-features">
                        <li>Ironing</li>
                        <li>Fold</li>
                        <li>Fabric Softener</li>
                        <li>Detergent</li>
                        <li>Dry</li>
                        <li>Bulky Wash</li>
                    </ul>
                </div>
            </div>
        </section>


        <section id="download" class="mobile-app-section">
            <h1>Our Mobile Application</h1>
            <div class="app-container">
                <!-- Left side: Image of mobile app -->
                <div class="app-image">
                    <img src="img/new_pic.png" alt="E&Q Laundry Shop Mobile App">
                </div>
                
                <!-- Right side: App description and download link -->
                <div class="app-description">
                    <h2><strong>E&Q LAUNDRY APP</strong> - Your Laundry, Our Priority.</h2>
                    <p>Get your laundry done with ease! Schedule pickups, track orders, and enjoy delivery right to your door—all through our easy-to-use app.</p>
                    <a href="https://drive.google.com/drive/folders/15zeGlpB0UxYcKNYltfwed5Ybn28TnMih" class="download-btn">Download now!</a>
                </div>
            </div>
        </section>


        
          <!-- Footer -->
  <!-- Footer -->
<footer>
    <div class="navigation">
        <div class="logo">
            <img src="img/logo new.png" alt="Logo">
            <div class="user-count">
    <span class="count-number"><?php echo $userCount; ?></span>
    <span class="count-label">Users in the Past 30 Days</span>
</div>


        </div>
        <a href="#back">Home</a>
        <a href="#us">About Us</a>
        <a href="#services">Services</a>
        <a href="#package">Package</a>
        <a href="#download">Download</a>
    </div>

    <div class="social-media">
        <a href="#"><img src="/img/fb.png" alt="Facebook"></a>
        <a href="#"><img src="/img/twitter.png" alt="Twitter"></a>
        <a href="#"><img src="img/ig.png" alt="Instagram"></a>
    </div>

    <p class="copyright">No copyright infringement</p>
</footer>



        
    
    </body>
</html>
