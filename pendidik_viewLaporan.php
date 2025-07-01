<?php
include 'config.php';

$ic_pelajar = $_GET['id_pelajar'] ?? null;
$bulan = $_GET['bulan'] ?? null;

if (!$ic_pelajar || !$bulan) {
    die("Parameter pelajar dan bulan tidak lengkap.");
}

$query = "SELECT pelajar.nama_pelajar, pelajar.ic_pelajar, laporan.tarikh_laporan, laporan.bulan,
    laporan.fizikal, laporan.deria_persekitaran, laporan.sahsiah, laporan.kreativiti, laporan.komunikasi, laporan.matematik_logik,
    laporan.fizikal_ulasan, laporan.deria_persekitaran_ulasan, laporan.sahsiah_ulasan, laporan.kreativiti_ulasan, laporan.komunikasi_ulasan, laporan.matematik_logik_ulasan,
    laporan.ulasan
    FROM laporan
    INNER JOIN pelajar ON laporan.ic_pelajar = pelajar.ic_pelajar
    WHERE pelajar.ic_pelajar = ? AND laporan.bulan = ?";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Kesalahan pada pernyataan SQL: " . $conn->error);
}
$stmt->bind_param("ss", $ic_pelajar, $bulan);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    die("Laporan untuk pelajar dan bulan ini tidak ditemukan.");
}

$stmt->close();

// Dapatkan nama pelajar (bagi header)
$query2 = "SELECT nama_pelajar FROM pelajar WHERE ic_pelajar = ?";
$stmt2 = $conn->prepare($query2);
$stmt2->bind_param("s", $ic_pelajar);
$stmt2->execute();
$result2 = $stmt2->get_result();
$pelajar = $result2->fetch_assoc();
$stmt2->close();
?>

<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Kemaskini Laporan Pelajar</title>
    <link rel="stylesheet" href="css/dashboard.css" />
    <style>
      /* Base styles */
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

textarea,
input[type=date],
input[type=month],
input[type=hidden] {
  width: 100%;
  padding: 8px;
  margin-bottom: 15px;
  border: 1px solid #ccc;
  border-radius: 6px;
  font-size: 14px;
  resize: vertical;
  box-sizing: border-box;
}

/* Inline date/month group */
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

/* Radio button groups */
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
.student-name {
  font-size: 20px;
  margin-bottom: 25px;
  color: #333;
  text-align: center;
}
    </style>
</head>
<body class="dashboard-container">
<div class="container">
    <header>
        <a href="pendidik_dashboard.php">Dashboard Pendidik</a>
        <a href="logout.php">Logout</a>
    </header>
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
    <h2 style="text-align: center; margin: 20px 0;">Kemaskini Laporan Pencapaian & Perkembangan Pelajar</h2>
    <div class="student-name">Nama Pelajar: <?php echo htmlspecialchars($pelajar['nama_pelajar']); ?></div>

        <form method="POST" action="pendidik_updateLaporan.php">
            <input type="hidden" name="id_pelajar" value="<?php echo htmlspecialchars($ic_pelajar); ?>">
            <input type="hidden" name="bulan" value="<?php echo htmlspecialchars($bulan); ?>">
            <input type="hidden" name="id_pendidik" value="<?php echo htmlspecialchars($id_pendidik); ?>">

            <div class="form-group-inline">
                <label for="tarikh_laporan">Tarikh Penilaian:</label>
                <input type="date" id="tarikh_laporan" name="tarikh_laporan" required value="<?php echo htmlspecialchars($data['tarikh_laporan']); ?>">

                <label for="bulan_input">Bulan Penilaian:</label>
                <input type="month" id="bulan_input" name="bulan_input" required value="<?php echo htmlspecialchars($data['bulan']); ?>">
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
                $skor_val = $data[$key];
                $ulasan_val = $data["{$key}_ulasan"];
                echo "<fieldset class='category-fieldset'>";
                echo "<legend>$label</legend>";
                echo "<textarea name='{$key}_ulasan' required>$ulasan_val</textarea>";
                echo "<div class='skor-group'>";
                for ($i = 1; $i <= 3; $i++) {
                    $checked = ($i == $skor_val) ? "checked" : "";
                    echo "<label><input type='radio' name='{$key}_skor' value='$i' $checked required> $i</label>";
                }
                echo "</div>";
                echo "</fieldset>";
            }
            ?>

            <label for="ulasan">Ulasan Keseluruhan:</label>
            <textarea id="ulasan" name="ulasan" required><?php echo htmlspecialchars($data['ulasan']); ?></textarea>

            <div class="button-group">
                <button type="submit" class="submit">Kemaskini</button>
                <a href="pendidikLaporan.php?id_pelajar=<?php echo urlencode($ic_pelajar); ?>" class="btn-kembali">Kembali</a>
            </div>
        </form>
    </main>
</div>

</body>
</html>
