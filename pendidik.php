<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ic_pendidik = $_POST['ic_pendidik'];
    $nama_pendidik = $_POST['nama_pendidik'];
    $email_pendidik = $_POST['email_pendidik'];
    $no_pendidik = $_POST['no_pendidik'];
    $umur = $_POST['umur'];
    $alamat_pendidik = $_POST['alamat_pendidik'];

    // Define password as educator's IC
    $password_pendidik = $ic_pendidik;

    // Handle file upload for sijil_pengajian
    $sijil_pengajian = $_FILES['sijil_pengajian']['name'];
    $sijil_pengajian_tmp = $_FILES['sijil_pengajian']['tmp_name'];
    $sijil_pengajian_path = 'uploads/' . $sijil_pengajian;
    move_uploaded_file($sijil_pengajian_tmp, $sijil_pengajian_path);

    // Handle file upload for kursus_kanak_kanak
    $kursus_kanak_kanak = $_FILES['kursus_kanak_kanak']['name'];
    $kursus_kanak_kanak_tmp = $_FILES['kursus_kanak_kanak']['tmp_name'];
    $kursus_kanak_kanak_path = 'uploads/' . $kursus_kanak_kanak;
    move_uploaded_file($kursus_kanak_kanak_tmp, $kursus_kanak_kanak_path);

    // Insert educator details into the PENDIDIK table
    $sql_educator = "INSERT INTO PENDIDIK (ic_pendidik, nama_pendidik, email_pendidik, no_pendidik, username, password, peranan_id, umur, alamat_pendidik, sijil_pengajian, kursus_kanak_kanak) 
                VALUES ('$ic_pendidik', '$nama_pendidik', '$email_pendidik', '$no_pendidik', '$email_pendidik', '$password_pendidik', 3, '$umur', '$alamat_pendidik', '$sijil_pengajian_path', '$kursus_kanak_kanak_path')";

    if ($conn->query($sql_educator) === TRUE) {
        // Success message after registration
        echo "<script>
                alert('Pendaftaran berjaya. Status pengesahan akan dihantar melalui email.');
                window.location.href = 'index.php';  // Redirect to index.html after OK
            </script>";
    } else {
        echo "Error inserting educator details: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Pendidik Baharu</title>
    <style>
        /* CSS Gaya */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
        }

        nav {
            background-color: #333;
            color: white;
            padding: 1rem;
            text-align: center;
        }

        h2, h3 {
            text-align: center;
            margin: 1rem 0;
        }

        form {
            max-width: 800px;
            margin: 2rem auto;
            padding: 1.5rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .container {
            display: flex;
            justify-content: space-between;
            gap: 2rem; /* Tambah jarak antara ruangan */
        }

        .column {
            flex: 1;
        }

        label {
            display: block;
            margin: 0.5rem 0 0.2rem;
        }

        input, select, textarea {
            width: 100%;
            padding: 0.5rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .buttons {
            display: flex;
            justify-content: center;
            gap: 1rem; /* Jarak antara butang */
        }

        button {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            background-color: #4caf50; /* Green color for Daftar button */
            color: white;
        }

        button.back {
            background-color: #f44336; /* Red color for the back button */
        }

        button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
<nav>
    <h2>Pendaftaran Pendidik Baharu</h2>
</nav>

<form method="post" enctype="multipart/form-data"> <!-- Added enctype for file upload -->
    <h3>Butiran Pendidik</h3>
    <div class="container">
        <div class="column">
            <label>Nama Pendidik:</label>
            <input type="text" name="nama_pendidik" required>
            <label>Kad Pengenalan:</label>
            <input type="text" name="ic_pendidik" class="form-control" required pattern="^[0-9]{12}$" title="Sila masukkan kad pengenalan yang sah dengan 12 digit sahaja">
            <label>Umur:</label>
            <input type="number" name="umur" required>
            <label>Alamat:</label>
            <input type="text" name="alamat_pendidik" required>
            <label>Email:</label>
            <input type="email" name="email_pendidik" required>
            <label>No Telefon:</label>
            <input type="text" name="no_pendidik" class="form-control" required pattern="^[0-9]{10,15}$" title="Sila masukkan nombor telefon yang sah dengan 10 hingga 15 digit sahaja">
            <label>Sijil Pengajian:</label>
            <input type="file" name="sijil_pengajian" accept=".pdf, .jpg, .jpeg, .png" required>
            <label>Kursus Pendidikan Awal Kanak-Kanak:</label>
            <input type="file" name="kursus_kanak_kanak" accept=".pdf, .jpg, .jpeg, .png" required>
        </div>
    </div>
    <div class="buttons">
        <button type="button" class="back" onclick="window.location.href='index.php'">Kembali</button>
        <button type="submit">Daftar</button>
    </div>
</form>
</body>
</html>


