<?php
session_start();
require_once 'config.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input for ibu bapa
    $id_ibubapa = $_POST['id_ibubapa'] ?? null;
    $pekerjaan_bapa = $_POST['pekerjaan_bapa'] ?? null;
    $pendapatan_bapa = $_POST['pendapatan_bapa'] ?? null;
    $email_bapa = $_POST['email_bapa'] ?? null;
    $telefon_bapa = $_POST['no_bapa'] ?? null;
    $pekerjaan_ibu = $_POST['pekerjaan_ibu'] ?? null;
    $pendapatan_ibu = $_POST['pendapatan_ibu'] ?? null;
    $email_ibu = $_POST['email_ibu'] ?? null;
    $telefon_ibu = $_POST['no_ibu'] ?? null;

    // Check for missing required fields
    if (!$id_ibubapa || !$pekerjaan_bapa || !$pendapatan_bapa || !$email_bapa || !$telefon_bapa || !$pekerjaan_ibu || !$pendapatan_ibu || !$email_ibu || !$telefon_ibu) {
        die("Critical fields are required!");
    }

    try {
        // Start a transaction
        $conn->begin_transaction();

        // Update IBUBAPA table with ibu bapa details
        $query = "
            UPDATE ibubapa
            SET 
                pekerjaan_bapa = ?, 
                pendapatan_bapa = ?, 
                email_bapa = ?, 
                no_bapa = ?, 
                pekerjaan_ibu = ?, 
                pendapatan_ibu = ?, 
                EmailIbu = ?, 
                no_ibu = ?
            WHERE id_ibubapa = ?";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Error preparing parent query: " . $conn->error);
        }
        $stmt->bind_param(
            "sissssssi",
            $pekerjaan_bapa,
            $pendapatan_bapa,
            $email_bapa,
            $telefon_bapa,
            $pekerjaan_ibu,
            $pendapatan_ibu,
            $email_ibu,
            $telefon_ibu,
            $id_ibubapa
        );
        if (!$stmt->execute()) {
            throw new Exception("Error updating parent details: " . $stmt->error);
        }
        $stmt->close();

        // Commit the transaction
        $conn->commit();

        // Redirect back to the profile page
        header("Location: profilIbubapa1.php");
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
