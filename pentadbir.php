<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ic_pentadbir = $_POST['ic_pentadbir'];
    $nama_pentadbir = $_POST['nama_pentadbir'];
    $email_pentadbir = $_POST['email_pentadbir'];


    // Define password as administrator's IC
    $password_pentadbir = $ic_pentadbir;

    // Insert administrator details into the PENTADBIR table
    $sql_admin = "INSERT INTO pentadbir (ic_pentadbir, nama_pentadbir, email_pentadbir,  username, password, peranan_id) 
                VALUES ('$ic_pentadbir', '$nama_pentadbir', '$email_pentadbir', '$email_pentadbir', '$password_pentadbir', 1)";

    if ($conn->query($sql_admin) === TRUE) {
        // Success message after registration
        echo "<script>
                alert('Successfully registered, you may login using email and IC');
                window.location.href = 'index.html';  // Redirect to index.html after OK
            </script>";
    } else {
        echo "Error inserting login credentials: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Pentadbir Baharu</title>
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
    <h2>Pendaftaran Pentadbir Baharu</h2>
</nav>

<form method="post" enctype="multipart/form-data"> <!-- Added enctype for file upload -->
    <h3>Butiran Pentadbir</h3>
    <div class="container">
        <div class="column">
            <label>Nama Pentadbir:</label>
            <input type="text" name="nama_pentadbir" required>
            <label>Kad Pengenalan:</label>
            <input type="text" name="ic_pentadbir" class="form-control" required pattern="^[0-9]{12}$" title="Sila masukkan kad pengenalan yang sah dengan 12 digit sahaja">
            <label>Email:</label>
            <input type="email" name="email_pentadbir" required>
    
        </div>
    </div>
    <div class="buttons">
        <button type="button" class="back" onclick="window.location.href='index.html'">Kembali</button>
        <button type="submit">Daftar</button>
    </div>
</form>
</body>
</html>
