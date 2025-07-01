<?php
session_start();
require_once 'config.php';
require_once 'stripe-php/init.php';

header('Content-Type: application/json');

// Read raw POST input
$input = json_decode(file_get_contents("php://input"), true);

// Check for required parameters
if (!isset($input['stripeToken'], $input['amount'], $input['ic_pelajar'], $input['jenis_yuran'], $input['kaedah'], $input['bulan'])) {
    echo json_encode(['error' => 'Incomplete payment data.']);
    exit();
}

$stripeToken = $input['stripeToken']; // PaymentMethod ID from frontend
$amount = 10; // Fixed amount of 10 RM
$stripeAmount = intval($amount * 100); // convert to sen for Stripe
$ic_pelajar = $input['ic_pelajar'];
$jenis_yuran = $input['jenis_yuran'];
$bulan = $input['bulan'];
$kaedah = $input['kaedah'];
$id_ibubapa = $input['id_ibubapa']; // Add this line to get id_ibubapa from the input
$tarikh = date("Y-m-d");

// Create the PaymentIntent using the PaymentMethod ID
try {
    $paymentIntent = \Stripe\PaymentIntent::create([
        'amount' => $stripeAmount,
        'currency' => 'myr',
        'payment_method' => $stripeToken,
        'confirm' => true,  // Add this line
        'automatic_payment_methods' => [
            'enabled' => true,
            'allow_redirects' => 'never'
        ]
    ]);
} catch (\Stripe\Exception\ApiErrorException $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit();
}

// Check if the payment requires additional action (e.g., 3D Secure)
if ($paymentIntent->status === 'requires_action' && $paymentIntent->next_action->type === 'use_stripe_sdk') {
    // Set status as pending for 3D Secure verification
    $status = 'pending';
    $query = "
    INSERT INTO yuran (ic_pelajar, id_ibubapa, tarikh, jenis_yuran, jumlah, kaedah, status, bulan)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo json_encode(['error' => 'Database prepare failed: ' . $conn->error]);
        exit();
    }
    
    $stmt->bind_param("ssssssss", $ic_pelajar, $id_ibubapa, $tarikh, $jenis_yuran, $amount, $kaedah, $status, $bulan);
    $stmt->execute();
    
    echo json_encode([
        'requires_action' => true,
        'payment_intent_client_secret' => $paymentIntent->client_secret
    ]);
    exit();
} elseif ($paymentIntent->status !== 'succeeded') {
    // Payment failed
    $status = 'failed';
    $query = "
    INSERT INTO yuran (ic_pelajar, id_ibubapa, tarikh, jenis_yuran, jumlah, kaedah, status, bulan)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
";

    $stmt = $conn->prepare($query);
    if (!$stmt) {
        echo json_encode(['error' => 'Database prepare failed: ' . $conn->error]);
        exit();
    }
    
    $stmt->bind_param("ssssssss", $ic_pelajar, $id_ibubapa, $tarikh, $jenis_yuran, $amount, $kaedah, $status, $bulan);
    $stmt->execute();
    
    echo json_encode(['error' => 'Payment not completed.']);
    exit();
}

// If payment is successful
$status = 'success';
$query = "
    INSERT INTO yuran (ic_pelajar, id_ibubapa, tarikh, jenis_yuran, jumlah, kaedah, status, bulan)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
";

$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(['error' => 'Database prepare failed: ' . $conn->error]);
    exit();
}

$stmt->bind_param("ssssssss", $ic_pelajar, $id_ibubapa, $tarikh, $jenis_yuran, $amount, $kaedah, $status, $bulan);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Payment successful and recorded.']);
} else {
    echo json_encode(['error' => 'Database insert failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();

?>
