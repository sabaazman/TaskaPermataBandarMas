<?php
// Include the Stripe PHP library
require_once('vendor/autoload.php');
\Stripe\Stripe::setApiKey('your_secret_key'); // Use your Stripe secret key

// Set the endpoint secret you get from your Stripe Dashboard
$endpoint_secret = 'your_webhook_endpoint_secret'; // Find this in your Stripe Dashboard

// Retrieve the request's body and the Stripe signature header
$payload = @file_get_contents("php://input");
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

// Verify the webhook signature to ensure it's from Stripe
try {
    $event = \Stripe\Webhook::constructEvent(
        $payload, $sig_header, $endpoint_secret
    );
} catch (\Stripe\Exception\SignatureVerificationException $e) {
    // Invalid signature
    http_response_code(400);
    exit();
}

// Handle the event based on its type
switch ($event->type) {
    case 'payment_intent.succeeded':
        $paymentIntent = $event->data->object; // Contains the payment intent details
        // Update payment status in your database to "success"
        $paymentIntentId = $paymentIntent->id;
        $amount = $paymentIntent->amount_received / 100; // Amount in MYR
        $status = 'success';
        
        // Update database record (example SQL query)
        $update_query = "UPDATE yuran SET status = ? WHERE payment_intent_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ss", $status, $paymentIntentId);
        $stmt->execute();
        
        echo "Payment Intent succeeded!";
        break;
    
    case 'payment_intent.payment_failed':
        $paymentIntent = $event->data->object;
        // Update payment status to "failed"
        $paymentIntentId = $paymentIntent->id;
        $status = 'failed';
        
        // Update database record (example SQL query)
        $update_query = "UPDATE yuran SET status = ? WHERE payment_intent_id = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ss", $status, $paymentIntentId);
        $stmt->execute();
        
        echo "Payment Intent failed!";
        break;

    // Handle other events if needed
    default:
        echo "Unhandled event type: " . $event->type;
}

// Respond with a success code
http_response_code(200);
?>