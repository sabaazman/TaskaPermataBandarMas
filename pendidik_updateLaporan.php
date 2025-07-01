<?php
include 'config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$id_pendidik = (int)$_SESSION['id_pendidik']; // pastikan integer

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ic_pelajar = $_POST['id_pelajar'] ?? null;
    if (!$ic_pelajar) {
        die("ID pelajar tidak ditemukan!");
    }

    // Dapatkan data dari form
    $tarikh_laporan = $_POST['tarikh_laporan'] ?? date('Y-m-d');
    $bulan = $_POST['bulan'] ?? date('Y-m');

    $categories = ['fizikal', 'deria_persekitaran', 'sahsiah', 'kreativiti', 'komunikasi', 'matematik_logik'];
    $skor = [];
    $ulasan_kategori = [];

    foreach ($categories as $cat) {
        $skor[$cat] = (int)($_POST["{$cat}_skor"] ?? 0);
        $ulasan_kategori[$cat] = $_POST["{$cat}_ulasan"] ?? '';
    }

    $ulasan = $_POST['ulasan'] ?? '';

    // Periksa sama ada laporan untuk pelajar dan bulan ini sudah ada
    $check_stmt = $conn->prepare("SELECT COUNT(*) FROM laporan WHERE ic_pelajar = ? AND bulan = ?");
    $check_stmt->bind_param("ss", $ic_pelajar, $bulan);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
        // Update rekod sedia ada
        $stmt = $conn->prepare("UPDATE laporan SET 
            id_pendidik = ?,
            tarikh_laporan = ?,
            fizikal = ?,
            deria_persekitaran = ?,
            sahsiah = ?,
            kreativiti = ?,
            komunikasi = ?,
            matematik_logik = ?,
            fizikal_ulasan = ?,
            deria_persekitaran_ulasan = ?,
            sahsiah_ulasan = ?,
            kreativiti_ulasan = ?,
            komunikasi_ulasan = ?,
            matematik_logik_ulasan = ?,
            ulasan = ?
            WHERE ic_pelajar = ? AND bulan = ?");
        if (!$stmt) {
            die("Kesalahan pada pernyataan SQL update: " . $conn->error);
        }
        $stmt->bind_param("issiiiiisssssssss",
            $id_pendidik,
            $tarikh_laporan,
            $skor['fizikal'],
            $skor['deria_persekitaran'],
            $skor['sahsiah'],
            $skor['kreativiti'],
            $skor['komunikasi'],
            $skor['matematik_logik'],
            $ulasan_kategori['fizikal'],
            $ulasan_kategori['deria_persekitaran'],
            $ulasan_kategori['sahsiah'],
            $ulasan_kategori['kreativiti'],
            $ulasan_kategori['komunikasi'],
            $ulasan_kategori['matematik_logik'],
            $ulasan,
            $ic_pelajar,
            $bulan
        );
    } else {
        // Insert rekod baru jika tiada
        $stmt = $conn->prepare("INSERT INTO laporan 
            (ic_pelajar, id_pendidik, tarikh_laporan, bulan,
             fizikal, deria_persekitaran, sahsiah, kreativiti, komunikasi, matematik_logik,
             fizikal_ulasan, deria_persekitaran_ulasan, sahsiah_ulasan, kreativiti_ulasan, komunikasi_ulasan, matematik_logik_ulasan,
             ulasan)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("Kesalahan pada pernyataan SQL insert: " . $conn->error);
        }
        $stmt->bind_param("sissiiiiissssssss",
            $ic_pelajar,
            $id_pendidik,
            $tarikh_laporan,
            $bulan,
            $skor['fizikal'],
            $skor['deria_persekitaran'],
            $skor['sahsiah'],
            $skor['kreativiti'],
            $skor['komunikasi'],
            $skor['matematik_logik'],
            $ulasan_kategori['fizikal'],
            $ulasan_kategori['deria_persekitaran'],
            $ulasan_kategori['sahsiah'],
            $ulasan_kategori['kreativiti'],
            $ulasan_kategori['komunikasi'],
            $ulasan_kategori['matematik_logik'],
            $ulasan
        );
    }

    if ($stmt->execute()) {
        echo "<script>alert('Laporan berjaya dikemaskini!'); window.location.href = 'laporan.php';</script>";
    } else {
        echo "<script>alert('Gagal kemaskini laporan. Error: " . $stmt->error . "'); history.back();</script>";
    }

    $stmt->close();
} else {
    echo "Invalid request method.";
}

$conn->close();
?>
