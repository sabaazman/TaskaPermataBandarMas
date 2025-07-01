<?php
include 'config.php'; // Include the database configuration
session_start(); // Start the session to store user data
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran</title>
    <!-- Bootstrap 5.3.0 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    
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
            background: rgba(58, 64, 101, 0.4);
            z-index: 0;
        }
    
        .container {
            background: rgba(255, 255, 255, 0.95);
            padding: 2.5rem;
            border-radius: 15px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 90%;
            transition: transform 0.3s ease;
            position: relative;
            z-index: 1;
            text-align: center;
        }
    
        .container:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.4);
        }
    
        h1 {
            color: #3a4065;
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 2rem;
            position: relative;
            padding-bottom: 0.5rem;
        }
    
        h1::after {
            content: "";
            position: absolute;
            bottom: 0;
            left: 25%;
            width: 50%;
            height: 3px;
            background: linear-gradient(135deg, #3a4065 0%, #4c5185 100%);
            border-radius: 3px;
        }
    
        .btn {
            margin: 1rem 0;
            width: 80%;
            padding: 1rem;
            font-size: 1.1rem;
            border-radius: 10px;
            border: none;
            transition: all 0.3s ease;
            font-weight: 600;
            position: relative;
            overflow: hidden;
        }
    
        .btn::before {
            content: "";
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }
    
        .btn:hover::before {
            left: 100%;
        }
    
        .btn-primary {
            background: linear-gradient(135deg, #3a4065 0%, #4c5185 100%);
            color: white;
        }
    
        .btn-success {
            background: linear-gradient(135deg, #11998e, #38ef7d);
            color: white;
        }
    
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
    
        .btn:active {
            transform: translateY(1px);
        }
    
        .btn i {
            margin-right: 10px;
        }
    
        /* Floating educational elements */
        .education-icon {
            position: absolute;
            opacity: 0.1;
            color: #3a4065;
            font-size: 2rem;
            z-index: -1;
            animation: float 15s infinite linear;
        }
    
        .icon-1 { top: 10%; left: 15%; animation-delay: 0s; }
        .icon-2 { top: 70%; left: 80%; animation-delay: 2s; }
        .icon-3 { top: 30%; left: 70%; animation-delay: 4s; }
    
        @keyframes float {
            0% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
            100% { transform: translateY(0) rotate(0deg); }
        }
    
        @media (max-width: 768px) {
            .container {
                padding: 2rem;
            }
            
            h1 {
                font-size: 1.8rem;
            }
            
            .btn {
                width: 90%;
                font-size: 1rem;
                padding: 0.8rem;
            }
        }
                    /* Add to your existing CSS */
            .btn-back {
                background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
                color: white;
                border-radius: 10px;
                padding: 1rem;
                font-size: 1.1rem;
                font-weight: 600;
                border: none;
                cursor: pointer;
                transition: all 0.3s ease;
                text-decoration: none;
                text-align: center;
                width: 80%;
                margin: 1rem 0 0;
                display: inline-block;
                position: relative;
                overflow: hidden;
            }

            .btn-back::before {
                content: "";
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
                transition: 0.5s;
            }

            .btn-back:hover::before {
                left: 100%;
            }

            .btn-back:hover {
                transform: translateY(-3px);
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            }

            .btn-back:active {
                transform: translateY(1px);
            }

            .btn-back i {
                margin-right: 10px;
            }
    </style>
</head>
<body>
    <div class="container">
        <h1>Pendaftaran</h1>
        <a href="daftar_ibubapa.php" class="btn btn-primary">
            <i class="fas fa-users"></i> Ibu Bapa
        </a>
        <a href="pendidik.php" class="btn btn-success">
            <i class="fas fa-chalkboard-teacher"></i> Pendidik
        </a>
        <a href="index.php" class="btn btn-back">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Bootstrap 5.3.0 JS and FontAwesome -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

</body>
</html>
