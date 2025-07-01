<?php
include 'config.php'; // Include database configuration
session_start(); // Start the session to store user data

// Check if a session variable for login error exists
if (isset($_SESSION['login_error'])) {
    $login_error = $_SESSION['login_error'];
    unset($_SESSION['login_error']); // Clear error after displaying it
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username']; // Email as the username
    $password = $_POST['password']; // IC as the password

    // Query for parent login (peranan_id = 2), with status approval check
    $sql_parent = "SELECT * FROM IBUBAPA 
                   WHERE username = '$username' 
                   AND password = '$password' 
                   AND peranan_id = 2";

    $result_parent = $conn->query($sql_parent);

    // Query for educator login (peranan_id = 3)
    $sql_educator = "SELECT * FROM PENDIDIK WHERE username = '$username' AND password = '$password' AND peranan_id = 3";
    $result_educator = $conn->query($sql_educator);

    // Query for admin login (peranan_id = 1)
    $sql_admin = "SELECT * FROM PENTADBIR WHERE username = '$username' AND password = '$password' AND peranan_id = 1";
    $result_admin = $conn->query($sql_admin);

    // Check if user is found in any of the tables and redirect based on role
    if ($result_parent->num_rows > 0) {
        $row = $result_parent->fetch_assoc();
        
        // Check the 'pengesahan' field in the IBUBAPA table
        if (is_null($row['pengesahan'])) {
            // If pengesahan is NULL (pending)
            $login_error = "Akaun anda masih dalam proses pengesahan oleh pentadbir. Sila tunggu.";
        } elseif ($row['pengesahan'] == 0) {
            // If pengesahan is 0 (rejected)
            $login_error = "Akaun anda telah ditolak oleh pentadbir.";
        } elseif ($row['pengesahan'] == 1) {
            // If pengesahan is 1 (approved)
            $_SESSION['username'] = $username; // Store username in session
            $_SESSION['id_ibubapa'] = $row['id_ibubapa']; // Store the parent ID in the session
            header("Location: ibubapa_dashboard.php"); // Redirect to parent dashboard
            exit();
        }

    } elseif ($result_educator->num_rows > 0) {
        $row = $result_educator->fetch_assoc();

        // Check 'pengesahan' field for pendidik
        if (is_null($row['pengesahan'])) {
            $login_error = "Akaun pendidik anda masih dalam proses pengesahan oleh pentadbir. Sila tunggu.";
        } elseif ($row['pengesahan'] == 0) {
            $login_error = "Akaun pendidik anda telah ditolak oleh pentadbir.";
        } elseif ($row['pengesahan'] == 1) {
            $_SESSION['username'] = $username;  // Store the username in the session
            $_SESSION['id_pendidik'] = $row['id_pendidik'];  // Store the educator's id_pendidik in the session
            header("Location: pendidik_dashboard.php");  // Redirect to educator dashboard
            exit();
        }

    } elseif ($result_admin->num_rows > 0) {
        $row = $result_admin->fetch_assoc();
        $_SESSION['username'] = $username; // Store username in session
        $_SESSION['id_pentadbir'] = $row['id_pentadbir'];  // Store the admin's id_pentadbir in session
        header("Location: pentadbir_dashboard.php"); // Redirect to admin dashboard
        exit();
    } else {
        // Invalid login
        $login_error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap 5.3.0 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- FontAwesome for Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

    <style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: url('https://images.unsplash.com/photo-1588072432836-e10032774350?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80') no-repeat center center fixed;
    background-size: cover;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
    position: relative;
}

/* Overlay to make text more readable */
body::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(58, 64, 101, 0.4); /* Slightly darker overlay for better contrast with kids image */
    z-index: 0;
}

.card {
    width: 100%;
    max-width: 350px;
    border-radius: 15px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
    background: rgba(255, 255, 255, 0.95);
    padding: 1.5rem;
    transition: transform 0.3s ease;
    position: relative;
    z-index: 1;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.4);
}

.card-header {
    background: linear-gradient(135deg, #3a4065 0%, #4c5185 100%);
    color: white;
    font-size: 1.3rem;
    font-weight: 600;
    text-align: center;
    padding: 1rem;
    border-radius: 12px;
    margin: -1.5rem -1.5rem 1.5rem -1.5rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.card-header::after {
    content: "";
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
    transform: rotate(30deg);
}

.form-label {
    font-weight: 500;
    color: #3a4065;
    margin-bottom: 0.3rem;
    display: block;
    font-size: 0.9rem;
}

.form-control {
    border-radius: 10px;
    padding: 0.6rem 1rem;
    font-size: 0.95rem;
    border: 1px solid #e1e5ee;
    margin-bottom: 1rem;
    transition: all 0.3s ease;
    width: 100%;
    box-sizing: border-box;
}

.form-control:focus {
    border-color: #4c5185;
    box-shadow: 0 0 0 0.25rem rgba(58, 64, 101, 0.25);
}

.button-container {
    display: flex;
    gap: 10px;
    margin-top: 0.5rem;
}

.btn-login, .btn-back {
    color: white;
    border-radius: 10px;
    padding: 0.8rem;
    font-size: 1rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    text-align: center;
    flex: 1;
}

.btn-login {
    background: linear-gradient(135deg, #3a4065 0%, #4c5185 100%);
}

.btn-back {
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
}

.btn-login:hover, .btn-back:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
}

.btn-back:hover {
    background: linear-gradient(135deg, #495057 0%, #343a40 100%);
}

.btn-login:active, .btn-back:active {
    transform: translateY(0);
}

/* Animation for form elements */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.card-body form > * {
    animation: fadeIn 0.5s ease forwards;
}

.card-body form > *:nth-child(1) { animation-delay: 0.1s; }
.card-body form > *:nth-child(2) { animation-delay: 0.2s; }
.card-body form > *:nth-child(3) { animation-delay: 0.3s; }
</style>
</head>
<body>

    <!-- Card for Login Form -->
    <div class="card">
        <div class="card-header">
            Taska Permata Bandar Mas
        </div>
        <div class="card-body">
            <h2 class="text-center mb-4">Log Masuk</h2>
            <?php if (isset($login_error)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $login_error; ?>
                </div>
            <?php endif; ?>
            <form method="post">
                <!-- Email input -->
                <div class="mb-3">
                    <label for="username" class="form-label">Email Address</label>
                    <input type="email" class="form-control" id="username" name="username" required>
                </div>
                <!-- Password input -->
                <div class="mb-3">
                    <label for="password" class="form-label">No Kad Pengenalan</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <!-- Login button -->
                <div class="button-container">
                    <a href="index.php" class="btn-back">Kembali</a>
                    <button type="submit" class="btn-login">Log Masuk</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS and FontAwesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>