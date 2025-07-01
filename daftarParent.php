<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Parent Details
    $ic_bapa = $_POST['ic_bapa'];
    $nama_bapa = $_POST['nama_bapa'];
    $pekerjaan_bapa = $_POST['pekerjaan_bapa'];
    $pendapatan_bapa = $_POST['pendapatan_bapa'];
    $email_bapa = $_POST['email_bapa']; //// Used as username
    $no_bapa = $_POST['no_bapa'];
    $ic_ibu = $_POST['ic_ibu'];
    $nama_ibu = $_POST['nama_ibu'];
    $pekerjaan_ibu = $_POST['pekerjaan_ibu'];
    $pendapatan_ibu = $_POST['pendapatan_ibu'];
    $no_ibu = $_POST['no_ibu'];
    $EmailIbu = $_POST['EmailIbu'];

    // Define password as father's IC
    $password_bapa = $ic_bapa;

    // Child Details
    $ic_pelajar = $_POST['ic_pelajar'];
    $nama_pelajar = $_POST['nama_pelajar'];
    $jantina = $_POST['jantina'];
    $alamat_semasa = $_POST['alamat_semasa'];
    $umur = $_POST['umur'];

    // Handle Image Upload
    $gambar_pelajar = ''; // Initialize the variable to store the file name

    if (isset($_FILES['gambar_pelajar']) && $_FILES['gambar_pelajar']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";

        // Create the uploads directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true); // Set directory permissions to writable
        }

        // Generate a unique file name
        $unique_id = uniqid(); // Unique identifier
        $original_name = basename($_FILES["gambar_pelajar"]["name"]);
        $target_file = $target_dir . $unique_id . "_" . $original_name;
        $gambar_pelajar = $unique_id . "_" . $original_name;

        // Check the file type
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png'];

        if (!in_array($imageFileType, $allowed_types)) {
            die("Only JPG, JPEG, PNG.");
        }

        // Move the uploaded file to the uploads directory
        if (move_uploaded_file($_FILES["gambar_pelajar"]["tmp_name"], $target_file)) {
            // Successfully uploaded
        } else {
            die("There was an error uploading the file.");
        }
    } else {
        die("Please upload a valid image file.");
    }

    // Insert Parent Details along with login credentials
    $sql_ibubapa = "INSERT INTO ibubapa (ic_bapa, nama_bapa, pekerjaan_bapa, pendapatan_bapa, email_bapa, no_bapa, ic_ibu, nama_ibu, pekerjaan_ibu, pendapatan_ibu, no_ibu, EmailIbu, username, password, peranan_id)
                    VALUES ('$ic_bapa', '$nama_bapa', '$pekerjaan_bapa', $pendapatan_bapa, '$email_bapa', '$no_bapa', '$ic_ibu', '$nama_ibu', '$pekerjaan_ibu', $pendapatan_ibu, '$no_ibu', '$EmailIbu', '$email_bapa', '$password_bapa', 2)";
    
    if ($conn->query($sql_ibubapa) === TRUE) {
        $ibubapa_id = $conn->insert_id;

        // Insert Child Details
        $sql_pelajar = "INSERT INTO pelajar (ic_pelajar, nama_pelajar, jantina, alamat_semasa, umur, gambar_pelajar, ibubapa_id)
                        VALUES ('$ic_pelajar', '$nama_pelajar', '$jantina', '$alamat_semasa', $umur, '$gambar_pelajar', $ibubapa_id)";
        
        if ($conn->query($sql_pelajar) === TRUE) {
            // Successful registration message
            echo "<script>
                    alert('Registration successful. You can now log in.');
                    window.location.href = 'index.html';  // Redirect to index.html after OK
                  </script>";
        } else {
            echo "Error inserting child details: " . $conn->error;
        }
    } else {
        echo "Error inserting parent details: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Pelajar Baharu</title>
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
            background-color: #4caf50;
            color: white;
        }

        button.back {
            background-color: #f44336; /* Merah untuk butang Kembali */
        }

        button:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <nav>
        <h2>Pendaftaran Pelajar Baharu</h2>
    </nav>

    <form method="post" enctype="multipart/form-data"> <!-- Added enctype for file upload -->
        <h3>Butiran Ibu Bapa</h3>
        <div class="container">
            <div class="column">
                <label>Nama Bapa:</label>
                <input type="text" name="nama_bapa" required>
                <label>No Kad Pengenalan Bapa:</label>
                <input type="text" name="ic_bapa" class="form-control" required pattern="^[0-9]{12}$" title="Sila masukkan kad pengenalan yang sah dengan 12 digit sahaja">
                <label>Pekerjaan Bapa:</label>
                <input type="text" name="pekerjaan_bapa">
                <label>Pendapatan Bapa:</label>
                <input type="number" step="0.01" name="pendapatan_bapa">
                <label>Email Bapa:</label>
                <input type="email" name="email_bapa" required>
                <label>No Telefon Bapa:</label>
                <input type="text" name="no_bapa" class="form-control" required pattern="^[0-9]{10}$" title="Sila masukkan nombor telefon yang sah dengan 10 hingga 15 digit sahaja">
            </div>
            <div class="column">
                <label>Nama Ibu:</label>
                <input type="text" name="nama_ibu">
                <label>No Kad Pengenalan Ibu:</label>
                <input type="text" name="ic_ibu" class="form-control" required pattern="^[0-9]{12}$" title="Sila masukkan kad pengenalan yang sah dengan 12 digit sahaja">
                <label>Pekerjaan Ibu:</label>
                <input type="text" name="pekerjaan_ibu">
                <label>Pendapatan Ibu:</label>
                <input type="number" step="0.01" name="pendapatan_ibu">
                <label>Email Ibu:</label>
                <input type="email" name="EmailIbu" required>
                <label>No Telefon Ibu:</label>
                <input type="text" name="no_ibu" class="form-control" required pattern="^[0-9]{10}$" title="Sila masukkan nombor telefon yang sah dengan 10 hingga 15 digit sahaja">
            </div>
        </div>

        <h3>Butiran Pelajar</h3>
        <div class="container">
            <div class="column">
                <label>Nama Pelajar:</label>
                <input type="text" name="nama_pelajar" required>
                <label>Kad MyKid Pelajar:</label>
                <input type="text" name="ic_pelajar" required>
                <label>Jantina:</label>
                <select name="jantina" required>
                    <option value="Lelaki">Lelaki</option>
                    <option value="Perempuan">Perempuan</option>
                </select>
                <label>Alamat Semasa:</label>
                <textarea name="alamat_semasa"></textarea>
                <label>Umur:</label>
                <input type="number" name="umur" required>
                <label>Gambar Pelajar:</label>
                <input type="file" name="gambar_pelajar" accept="image/*" required> <!-- New field for image -->
            </div>
        </div>

        <div class="buttons">
            <button type="button" class="back" onclick="window.location.href='index.html'">Kembali</button>
            <button type="submit">Daftar</button>
        </div>
    </form>
</body>
</html>