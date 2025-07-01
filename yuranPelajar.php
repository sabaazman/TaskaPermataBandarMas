<?php
session_start();
require_once 'config.php'; // Database connection
require_once 'stripe-php/init.php';  // Include the Stripe PHP library

// Your Stripe API Key (make sure to use your actual secret key)
\Stripe\Stripe::setApiKey('sk_test_51RFybbATeylkTzmOsUKBO2xJt5NHPZCrBY0KMDJ3MMLEawx3kajAcsREUqHUZl8U69t0JtxQmLvhmPoLwAgTlZ8d00QAFF7BoE');

// Ensure the parent is logged in and has a valid ID
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$id_ibubapa = $_SESSION['id_ibubapa']; // Get the logged-in parent's ID

// Get the student ID (ic_pelajar) from the URL
$ic_pelajar = isset($_GET['ic']) ? $_GET['ic'] : '';

// Get the selected month from URL, default to current month if not set
$selected_month = isset($_GET['bulan']) ? intval($_GET['bulan']) : date('n');

// Query to get the student's name and parent's id_ibubapa
$query = "
    SELECT pelajar.nama_pelajar, ibubapa.id_ibubapa
    FROM pelajar
    INNER JOIN ibubapa ON pelajar.ibubapa_id = ibubapa.id_ibubapa
    WHERE pelajar.ic_pelajar = ?
";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare statement failed: " . $conn->error);
}

$stmt->bind_param("s", $ic_pelajar);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the student's name and parent's id_ibubapa
$student_name = '';
$parent_id = '';
if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    $student_name = $data['nama_pelajar'];
    $parent_id = $data['id_ibubapa']; // Parent's ID
} else {
    die("Student not found.");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bulan = $_POST['bulan'];
    $jumlah = $_POST['jumlah'];
    $kaedah = $_POST['kaedah'];
    $bank = $_POST['bank'];
    $jenis_yuran = $_POST['jenis_yuran'];

    // Convert amount to cents (Stripe expects the amount in the smallest currency unit)
    $amount_in_cents = $jumlah * 100;

    // Create a Payment Intent with card as the payment method
    try {
        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => $amount_in_cents,
            'currency' => 'myr',  // Malaysian Ringgit
            'payment_method_types' => ['card'],  // Use card
        ]);

        // Send the client secret to the front end
        echo json_encode([
            'clientSecret' => $paymentIntent->client_secret
        ]);
    } catch (\Stripe\Exception\ApiErrorException $e) {
        echo json_encode([
            'error' => $e->getMessage()
        ]);
    }

    // Insert the payment record into the database (status will default to 'pending')
    $insert_query = "
    INSERT INTO yuran (ic_pelajar, id_ibubapa, jumlah, kaedah, bank, jenis_yuran, bulan)
    VALUES (?, ?, ?, ?, ?, ?, ?)
";

    $stmt = $conn->prepare($insert_query);
    if (!$stmt) {
        die("Prepare statement failed: " . $conn->error);
    }

    $stmt->bind_param("ssdsss", $ic_pelajar, $parent_id, $jumlah, $kaedah, $bank, $jenis_yuran, $bulan);

    if ($stmt->execute()) {
        echo "<script>alert('Pembayaran berjaya disimpan!'); window.location.href = 'yuran.php';</script>";
    } else {
        echo "<script>alert('Gagal menyimpan pembayaran. Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Yuran - Pelajar</title>
    <script src="https://js.stripe.com/v3/"></script>
    <link rel="stylesheet" href="css/dashboard.css">
    <style>
        /* Base styles */
        body {
            margin: 0;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #f8f9fa;
            color: #333;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
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
        }

        nav ul li a {
            color: #3a4065 !important;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
            border-radius: 6px;
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

        .dropdown li a:hover {
            background: rgba(58, 64, 101, 0.1);
            color: #4e54c8;
        }

        /* Main content styling */
        main {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-top: 2rem;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            nav ul {
                flex-direction: column;
                padding: 1rem !important;
            }

            .dropdown {
                position: static;
                box-shadow: none;
                padding: 0 !important;
            }

            .container {
                width: 95%;
                padding: 10px;
            }
        }
        * Styling for inputs, selects, and buttons */
        .label {
            text-align: left;
            font-weight: bold;
        }

        select, input[type="text"], input[type="date"], input[type="number"] {
            width: 70%; /* Adjust width to keep the form elements compact */
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
    </style>
</head>
<body class="dashboard-container">
    <div class="container">
        <!-- Header -->
        <header>
            <a href="ibubapa_dashboard.php"><span>Dashboard Ibu Bapa</span></a>
            <a href="logout.php">Logout</a>
        </header>

        <!-- Navigation -->
        <nav>
            <ul>
            <li class="dropdown-parent">
                    <a href="#">Profil <span class="dropdown-icon">&#x25BC;</span></a>
                    <ul class="dropdown">
                        <li><a href="profilIbubapa1.php">Ibubapa</a></li>
                        <li><a href="pelajar.php">Pelajar</a></li>
                        <li><a href="ibubapa_daftar_pelajarBaru.php">Pendaftaran Pelajar Baru</a></li>
                                    </ul>
                </li>
                <li><a href="yuran.php">Yuran</a></li>
                <li><a href="display_jadual.php">Jadual Pelajar</a></li>
                <li><a href="ibubapaLaporan.php">Laporan</a></li>
            </ul>
        </nav>
        <main>
        <h2 style="text-align: center; margin: 20px 0;">Pembayaran Yuran</h2>
                <form method="post" action="payment_processing.php" id="payment-form">
                <!-- Input tersembunyi -->
                <input type="hidden" name="ic_pelajar" value="<?php echo htmlspecialchars($ic_pelajar); ?>">
        <table>
            <tr>
                <td class="label">Nama Pelajar:</td>
                <td><input type="text" name="nama_pelajar" value="<?php echo $student_name; ?>" readonly></td>
            </tr>
            <tr>
                <td class="label">Tarikh:</td>
                <td><input type="date" name="tarikh" value="<?php echo date('Y-m-d'); ?>" readonly></td>
            </tr>
            <tr>
            <tr>
                    <td class="label">Bulan:</td>
                    <td>
                        <input type="hidden" name="bulan" value="<?php echo $selected_month; ?>">
                        <span>Bulan <?php echo $selected_month; ?></span>
                    </td>
                </tr>
            </tr>
            <tr>
                <td class="label">Jenis Yuran:</td>
                <td><select name="jenis_yuran" required>
                    <option value="">Select Yuran</option>
                    <option value="Pendaftaran">Pendaftaran</option>
                    <option value="Bulanan">Bulanan</option>
                </select></td>
            </tr>
            <tr>
                <td class="label">Jumlah:</td>
                <td><input type="hidden" name="jumlah" id="amount" value="10" readonly>
                <span>RM 10</span></td>
            </tr>
            <tr>
                <td class="label">Kaedah Pembayaran:</td>
                <td><input type="hidden" name="kaedah" value="card"><p>Card Payment Method</p></td>
            </tr>
            <!---comment this section--->
            <!--
            <tr>
                <td class="label">Bank:</td>
                <td><select name="bank" id="bank" required>
                    <option value="">Select Bank</option>
                    <option value="Maybank">Maybank</option>
                    <option value="CIMB">CIMB</option>
                    <option value="Public Bank">Public Bank</option>
                </select></td>
            </tr>
           
            <tr>
                <td class="label">Payment Details:</td>
                <td>
                    <div id="card-element"></div>
                    <div id="card-errors" role="alert"></div>
                </td>
            </tr>
            --->
             <tr>
                <td class="label">Card Details:</td>
                <td>
                    <div id="card-element" autocomplete="off" data-stripe="false"></div>
                    <div id="card-errors" role="alert"></div>
                    <input type="hidden" name="id_ibubapa" value="<?php echo $parent_id; ?>">
                </td>
            </tr>
        </table>
        <div style="text-align: center; margin-top: 20px;">
        <button type="submit" class="submit-btn" style="background-color: #4CAF50 !important; color: white !important; padding: 10px 20px !important; border: none !important; border-radius: 5px !important; font-size: 16px !important; cursor: pointer !important; margin: 0 5px !important;">
    Submit
        </button>
        <a href="yuran.php?bulan=<?php echo urlencode($selected_month); ?>" class="back-btn" style="background-color: #dc3545; color: white; padding: 10px 20px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; text-decoration: none; display: inline-block; margin: 0 5px;">Back</a>

        </div>
    </form>

     
   <script>
   var stripe = Stripe('pk_test_51RFybbATeylkTzmOcNVnhOFQRHnQ9aE7AUxyKPCIfvfOAybxfR5mYzvs6SpZovOEp7xEtS3C7kI4LaGaH86TmOLl00qg5E6UAV');
var elements = stripe.elements();

// Create card element with saved card disabled
var card = elements.create('card', {
    hidePostalCode: true,
    savedCards: false,
    disableLink: true
});

// Mount the card element
card.mount('#card-element');

// Add autocomplete="off" to prevent browser autofill
document.getElementById('card-element').setAttribute('autocomplete', 'off');
document.getElementById('card-element').setAttribute('data-stripe', 'false');

// Clear the card element after successful payment
function clearCardElement() {
    card.clear();
    document.getElementById('card-errors').textContent = '';
}

var form = document.getElementById('payment-form');
form.addEventListener('submit', function (event) {
    event.preventDefault();

    stripe.createPaymentMethod({
        type: 'card',
        card: card,
    }).then(function (result) {
        if (result.error) {
            document.getElementById('card-errors').textContent = result.error.message;
        } else {
            fetch('payment_processing.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    stripeToken: result.paymentMethod.id,
                    amount: document.getElementById('amount').value,
                    ic_pelajar: document.getElementsByName('ic_pelajar')[0].value,
                    // Fix this line - change kaedahbulan to bulan
                    bulan: document.getElementsByName('bulan')[0].value,  // FIXED
                    jenis_yuran: document.getElementsByName('jenis_yuran')[0].value,
                    kaedah: document.getElementsByName('kaedah')[0].value,
                    id_ibubapa: document.getElementsByName('id_ibubapa')[0].value
                })
            }).then(function (response) {
                return response.json();
            }).then(function (paymentIntent) {
                if (paymentIntent.error) {
                    document.getElementById('card-errors').textContent = paymentIntent.error;
                } else if (paymentIntent.requires_action) {
                    stripe.confirmCardPayment(paymentIntent.payment_intent_client_secret).then(function(result) {
                        if (result.error) {
                            document.getElementById('card-errors').textContent = result.error.message;
                        } else if (result.paymentIntent.status === 'succeeded') {
                            clearCardElement(); // Clear card after successful payment
                            alert('Pembayaran berjaya disimpan!');
                            window.location.href = 'yuran.php?bulan=' + encodeURIComponent(document.getElementsByName('bulan')[0].value);
                        }
                    });
                } else if (paymentIntent.success) {
                    clearCardElement(); // Clear card after successful payment
                    alert('Pembayaran berjaya disimpan!');
                    window.location.href = 'yuran.php?bulan=' + encodeURIComponent(document.getElementsByName('bulan')[0].value);
                }
            });
        }
    });
});
</script>

</body>
</html>