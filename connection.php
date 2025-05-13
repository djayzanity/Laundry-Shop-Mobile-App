<?php
$host = '127.0.0.1';  
$dbname = 'enq11';      
$username = 'root';   
$password = '';       

try {
    // Initialize the PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
  
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
   
} catch (PDOException $e) {
   
    die("Connection failed: " . $e->getMessage());
}
?>
