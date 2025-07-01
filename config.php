<?php
$servername = "localhost";
$username = "root"; // Adjust as needed
$password = ""; // Adjust as needed
$dbname = "taska_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Include Stripe PHP library (adjust the path according to your folder structure)
require_once 'stripe-php/init.php';  // Correct the path to match your file structure

// Your Stripe API Key (make sure to use your actual secret key)
\Stripe\Stripe::setApiKey('sk_test_51RFybbATeylkTzmOsUKBO2xJt5NHPZCrBY0KMDJ3MMLEawx3kajAcsREUqHUZl8U69t0JtxQmLvhmPoLwAgTlZ8d00QAFF7BoE');
?>
