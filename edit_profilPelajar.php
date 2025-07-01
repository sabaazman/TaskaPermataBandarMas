<?php
session_start();
require_once 'config.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $ic_pelajar = $_POST['ic_pelajar'] ?? null;
    $alamat_semasa = $_POST['alamat_semasa'] ?? null;
    $umur = $_POST['umur'] ?? null;
    $alahan = $_POST['Alahan'] ?? null;
    $nama_pelajar = $_POST['nama_pelajar'] ?? null;

    // Check for missing required fields
    if (!$ic_pelajar || !$alamat_semasa || !$umur || !$alahan|| !$nama_pelajar) {
        die("Critical fields (ic_pelajar, alamat_semasa, umur, Alahan, nama_pelajar) are required!");
    }

    try {
        // Start a transaction
        $conn->begin_transaction();

        // Update PELAJAR table with new data
        $query = "
            UPDATE pelajar
            SET alamat_semasa = ?, umur = ?, Alahan = ?, nama_pelajar = ?
            WHERE ic_pelajar = ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Error preparing student update query: " . $conn->error);
        }
        $stmt->bind_param("sssss", $alamat_semasa, $umur, $alahan, $nama_pelajar, $ic_pelajar);
        if (!$stmt->execute()) {
            throw new Exception("Error updating student details: " . $stmt->error);
        }
        $stmt->close();

        // Commit the transaction
        $conn->commit();

        // Redirect back to the profile page
        header("Location: profilPelajar.php?ic=" . urlencode($ic_pelajar)); 
        exit();
    } catch (Exception $e) {
        // Rollback the transaction in case of error
        $conn->rollback();
        die("Transaction failed: " . $e->getMessage());
    }
} else {
    die("Invalid request method.");
}
?>
