<?php
include 'config.php';

// Dapatkan ID Jadual daripada parameter URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Ambil data jadual berdasarkan ID
    $query = "SELECT * FROM jadual WHERE IdJadual = $id";
    $result = mysqli_query($conn, $query);
    $data = mysqli_fetch_assoc($result);
}

// Proses kemaskini data
if ($_POST) {
    $masa = $_POST['masa'];
    $isnin = $_POST['isnin'];
    $selasa = $_POST['selasa'];
    $rabu = $_POST['rabu'];
    $khamis = $_POST['khamis'];
    $jumaat = $_POST['jumaat'];

    $update_query = "UPDATE jadual SET 
                        Masa = '$masa', 
                        Isnin = '$isnin', 
                        Selasa = '$selasa', 
                        Rabu = '$rabu', 
                        Khamis = '$khamis', 
                        Jumaat = '$jumaat'
                     WHERE IdJadual = $id";

    mysqli_query($conn, $update_query);

    echo "<script>alert('Jadual berjaya dikemaskini!');</script>";
    header("Location: admin_jadual.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kemaskini Jadual</title>
</head>
<body>
    <h1>Kemaskini Jadual</h1>
    <form method="POST" action="">
        <label for="masa">Masa:</label>
        <input type="text" id="masa" name="masa" value="<?php echo $data['Masa']; ?>" required><br><br>

        <label for="isnin">Isnin:</label>
        <input type="text" id="isnin" name="isnin" value="<?php echo $data['Isnin']; ?>"><br><br>

        <label for="selasa">Selasa:</label>
        <input type="text" id="selasa" name="selasa" value="<?php echo $data['Selasa']; ?>"><br><br>

        <label for="rabu">Rabu:</label>
        <input type="text" id="rabu" name="rabu" value="<?php echo $data['Rabu']; ?>"><br><br>

        <label for="khamis">Khamis:</label>
        <input type="text" id="khamis" name="khamis" value="<?php echo $data['Khamis']; ?>"><br><br>

        <label for="jumaat">Jumaat:</label>
        <input type="text" id="jumaat" name="jumaat" value="<?php echo $data['Jumaat']; ?>"><br><br>

        <button type="submit">Kemaskini</button>
        <a href="admin_jadual.php"><button type="button">Kembali</button></a>
    </form>
</body>
</html>
