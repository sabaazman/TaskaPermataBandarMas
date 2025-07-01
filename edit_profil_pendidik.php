<?php
session_start();
require_once 'config.php'; // Sambungan database

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $ic_pendidik = $_POST['ic_pendidik'] ?? null;
    $email_pendidik = $_POST['email_pendidik'] ?? null;
    $no_pendidik = $_POST['no_pendidik'] ?? null;
    $umur = $_POST['umur'] ?? null;
    $alamat_pendidik = $_POST['alamat_pendidik'] ?? null;

    // Check for required fields
    if (!$ic_pendidik || !$email_pendidik || !$no_pendidik || !$umur || !$alamat_pendidik) {
        die("All fields are required!");
    }

    try {
        // Update PENDIDIK table
        $query = "
            UPDATE PENDIDIK
            SET 
                email_pendidik = ?, 
                no_pendidik = ?,
                umur = ?,
                alamat_pendidik = ?
            WHERE ic_pendidik = ?";

        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Error preparing query: " . $conn->error);
        }

        $stmt->bind_param("ssiss", $email_pendidik, $no_pendidik, $umur, $alamat_pendidik, $ic_pendidik);
        if (!$stmt->execute()) {
            throw new Exception("Error executing query: " . $stmt->error);
        }

        $stmt->close();

        // Redirect back to profile page
        header("Location: profilPendidik.php");
        exit();
    } catch (Exception $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    die("Invalid request method.");
}
