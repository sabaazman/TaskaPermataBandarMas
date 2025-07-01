<?php

include 'config.php';
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$id_pendidik = $_SESSION['id_pendidik'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ic_pelajar = $_POST['id_pelajar'];
    $id_pendidik = (int)$_SESSION['id_pendidik']; // pastikan integer
    $tarikh_laporan = $_POST['tarikh_laporan'];
    $bulan = $_POST['bulan']; // biar dalam format YYYY-MM, VARCHAR(20) sesuai

    $categories = ['fizikal', 'deria_persekitaran', 'sahsiah', 'kreativiti', 'komunikasi', 'matematik_logik'];
    $skor = [];
    $ulasan_kategori = [];

    foreach ($categories as $cat) {
        $skor[$cat] = (int)($_POST["{$cat}_skor"] ?? 0);
        $ulasan_kategori[$cat] = $_POST["{$cat}_ulasan"] ?? '';
    }

    $ulasan = $_POST['ulasan'] ?? '';

    $stmt = $conn->prepare("INSERT INTO laporan 
        (ic_pelajar, id_pendidik, tarikh_laporan, bulan, 
         fizikal, deria_persekitaran, sahsiah, kreativiti, komunikasi, matematik_logik,
         fizikal_ulasan, deria_persekitaran_ulasan, sahsiah_ulasan, kreativiti_ulasan, komunikasi_ulasan, matematik_logik_ulasan,
         ulasan)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        die("Kesalahan pada pernyataan SQL: " . $conn->error);
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

    if ($stmt->execute()) {
        echo "<script>alert('Laporan berjaya disimpan!'); window.location.href = 'laporan.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan laporan. Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

// Fetch student name
$ic_pelajar = isset($_GET['id_pelajar']) ? $_GET['id_pelajar'] : null;
if (!$ic_pelajar) {
    die("IC Pelajar tidak ditemukan!");
}

$query = "SELECT nama_pelajar FROM pelajar WHERE ic_pelajar = ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Kesalahan pada pernyataan SQL: " . $conn->error);
}
$stmt->bind_param("s", $ic_pelajar);
$stmt->execute();
$result = $stmt->get_result();
$pelajar = $result->fetch_assoc();
if (!$pelajar) {
    $pelajar = ['nama_pelajar' => 'Pelajar tidak ditemukan'];
}
$stmt->close();

?>


<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendidik Laporan</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <style>
    /* Base styles (merged from both sets) */
body {
  margin: 0;
  font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
  background: #f8f9fa;
  color: #333;
}

.container {
  max-width: 900px;
  margin: 30px auto;
  padding: 0 20px;
}

/* Header styling */
header {
  background: linear-gradient(135deg, #3a4065 0%, #4e54c8 100%);
  padding: 1rem;
  display: flex;
  justify-content: space-between;
  align-items: center;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

header a {
  color: white;
  text-decoration: none;
  font-weight: 600;
  transition: all 0.3s ease;
}

header a:hover {
  opacity: 0.9;
  transform: translateY(-1px);
}

/* Navigation styling */
nav {
  background: #ffffff;
  border-radius: 8px;
  margin: 1rem 0;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
}

nav ul {
  background: transparent !important;
  padding: 0 !important;
  margin: 0;
  display: flex;
  gap: 0.5rem;
  list-style-type: none;
}

nav ul li a {
  color: #3a4065 !important;
  padding: 1rem 1.5rem;
  transition: all 0.3s ease;
  border-radius: 6px;
  text-decoration: none;
  display: flex;
  align-items: center;
}

nav ul li a:hover {
  background: rgba(58, 64, 101, 0.1);
  transform: translateY(-1px);
}

nav ul li {
  position: relative;
}

.dropdown-parent > a {
  display: flex;
  align-items: center;
  gap: 5px;
}

.dropdown {
  position: absolute;
  top: 100%;
  left: 0;
  background-color: #ffffff !important;
  min-width: 200px;
  border-radius: 8px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
  border: 1px solid rgba(58, 64, 101, 0.1);
  padding: 0.5rem;
  display: none;
  z-index: 1000;
}

.dropdown-parent:hover .dropdown {
  display: block;
}

.dropdown li {
  width: 100%;
  margin: 0;
}

.dropdown li a {
  padding: 0.75rem 1rem;
  display: block;
  color: #3a4065;
  text-decoration: none;
  border-radius: 4px;
  margin: 2px;
}

/* Headings */
h2 {
  font-size: 26px;
  text-align: center;
  margin-bottom: 15px;
}

.student-name {
  font-size: 20px;
  margin-bottom: 25px;
  color: #333;
  text-align: center;
}

/* Form styling */
form {
  background-color: #fff;
  padding: 25px 30px;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgb(0 0 0 / 0.1);
  max-width: 800px;
  margin: 0 auto;
}

label {
  font-weight: bold;
  margin-bottom: 5px;
  display: block;
}

textarea {
  width: 98%;
  padding: 8px;
  margin-bottom: 15px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
  resize: vertical;
  min-height: 65px;
}

/* Input date and month inline group */
.form-group-inline {
  display: flex;
  gap: 25px;
  align-items: center;
  margin-bottom: 25px;
}

.form-group-inline label {
  min-width: 130px;
  font-weight: 600;
}

/* Category fieldset */
fieldset.category-fieldset {
  border: 1px solid #ccc;
  border-radius: 8px;
  margin-bottom: 28px;
  padding: 18px 20px 22px;
}

fieldset.category-fieldset legend {
  font-weight: 700;
  font-size: 17px;
  padding: 0 10px;
}

/* Skor radio group horizontal */
.skor-group {
  display: flex;
  gap: 25px;
  margin: 12px 0 15px;
}

.skor-group label {
  cursor: pointer;
  font-weight: 600;
  user-select: none;
}

/* Buttons */
.button-group {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 25px;
}

button.submit {
  background-color: #28a745; /* Green background */
  color: #fff;
  border: none;
  padding: 11px 30px;
  font-size: 17px;
  border-radius: 6px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

button.submit:hover {
  background-color: #218838; /* Darker green on hover */
}

.btn-kembali {
  background-color: #007bff;
  color: #fff;
  text-decoration: none;
  padding: 11px 30px;
  border-radius: 6px;
  font-size: 17px;
  display: inline-block;
  text-align: center;
}

.btn-kembali:hover {
  background-color: #0056b3;
}

</style>
</head>
<body class="dashboard-container">
    <div class="container">
        <!-- Header -->
        <header>
            <a href="pendidik_dashboard.php"><span>Dashboard Pendidik</span></a>
            <a href="logout.php">Logout</a>
        </header>

        <!-- Navigation -->
        <nav>
            <ul>
            <li class="dropdown-parent">
                    <a href="#">Profil <span class="dropdown-icon">&#x25BC;</span></a>
                    <ul class="dropdown">
                        <li><a href="profilPendidik.php">Pendidik</a></li>
                        <li><a href="pendidikPelajar.php">Pelajar</a></li>
                                    </ul>
                </li>
                <li><a href="yuranPendidik.php">Yuran</a></li>
                <li><a href="display_jadualPendidik.php">Jadual Pelajar</a></li>
                <li><a href="rpa.php">RPA</a></li>
                <li><a href="laporan.php">Laporan</a></li>
            </ul>
        </nav>
        <main>
      <h2 style="text-align: center; margin: 20px 0;">Laporan Pencapaian & Perkembangan Pelajar</h2>
      <div class="student-name">Nama Pelajar: <?php echo htmlspecialchars($pelajar['nama_pelajar']); ?></div>
      <form method="POST" action="">
        <input type="hidden" name="id_pelajar" value="<?php echo htmlspecialchars($ic_pelajar); ?>">
        <input type="hidden" name="id_pendidik" value="<?php echo htmlspecialchars($id_pendidik); ?>">

        <div class="form-group-inline">
          <label for="tarikh_laporan">Tarikh Penilaian:</label>
          <input type="date" id="tarikh_laporan" name="tarikh_laporan" required value="<?php echo date('Y-m-d'); ?>">
          
          <label for="bulan">Bulan Penilaian:</label>
          <input type="month" id="bulan" name="bulan" required value="<?php echo date('Y-m'); ?>">
        </div>

        <?php
        $categories = [
          'fizikal' => 'Perkembangan Fizikal',
          'deria_persekitaran' => 'Perkembangan Deria & Pemahaman Dunia Persekitaran',
          'sahsiah' => 'Perkembangan Sahsiah, Sosio-Emosi Dan Kerohanian',
          'kreativiti' => 'Kreativiti & Perkembangan Estetika',
          'komunikasi' => 'Perkembangan Bahasa Komunikasi & Literasi Awal',
          'matematik_logik' => 'Perkembangan Awal Matematik & Pemikiran Logik'
        ];

        foreach ($categories as $key => $label) {
          echo "<fieldset class='category-fieldset'>";
          echo "<legend>$label</legend>";
          echo "<textarea name='{$key}_ulasan' placeholder='Ulasan pendek untuk $label...'></textarea>";
          echo "<div class='skor-group'>";
          echo "<label><input type='radio' name='{$key}_skor' value='1' required> 1 - Dengan Bimbingan</label>";
          echo "<label><input type='radio' name='{$key}_skor' value='2' required> 2 - Sedikit Bimbingan</label>";
          echo "<label><input type='radio' name='{$key}_skor' value='3' required> 3 - Tanpa Bimbingan</label>";
          echo "</div>";
          echo "</fieldset>";
        }
        ?>

        <label for="ulasan">Ulasan Keseluruhan:</label>
        <textarea id="ulasan" name="ulasan" placeholder="Masukkan ulasan keseluruhan..."></textarea>

        <div class="button-group">
          <button type="submit" class="submit">Simpan</button>
          <a href="laporan.php" class="btn-kembali">Kembali</a>
        </div>
      </form>
    </main>
  </div>
</body>
</html>

<?php
$conn->close();
?>