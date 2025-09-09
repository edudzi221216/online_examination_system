<!--Student Login page-->
<!DOCTYPE html>
<html>
    
<head>
        <title>Student Login</title>
        <meta content="width=device-width, initial-scale=1" name="viewport"/>
        <meta charset="UTF-8">
        <link href='https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap' rel='stylesheet'>
        <link href="assets/plugins/fontawesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
        <link href="assets/css/modern-design-system.css" rel="stylesheet" type="text/css"/>
        <style>
            .redirect-container {
                min-height: 100vh;
                background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
                display: flex;
                align-items: center;
                justify-content: center;
                text-align: center;
                color: white;
                padding: 20px;
            }
            
            .redirect-card {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                border-radius: 20px;
                padding: 40px;
                max-width: 500px;
                width: 100%;
            }
            
            .redirect-icon {
                font-size: 64px;
                margin-bottom: 20px;
                opacity: 0.9;
            }
            
            .redirect-title {
                font-size: 28px;
                font-weight: 600;
                margin-bottom: 15px;
            }
            
            .redirect-message {
                font-size: 16px;
                margin-bottom: 30px;
                opacity: 0.9;
            }
            
            .redirect-btn {
                background: white;
                color: #4facfe;
                padding: 12px 30px;
                border: none;
                border-radius: 25px;
                font-size: 16px;
                font-weight: 600;
                text-decoration: none;
                display: inline-block;
                transition: all 0.3s ease;
            }
            
            .redirect-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 10px 20px rgba(0,0,0,0.2);
                color: #4facfe;
                text-decoration: none;
            }
            
            .auto-redirect {
                margin-top: 20px;
                font-size: 14px;
                opacity: 0.8;
            }
        </style>
</head>

<body>
    <div class="redirect-container">
        <div class="redirect-card">
            <i class="fa fa-user-graduate redirect-icon"></i>
            <h1 class="redirect-title">Student Portal</h1>
            <p class="redirect-message">
                Welcome to the Examination Management System.<br>
                Please use the unified login page to access your account.
            </p>
            <a href="login.php" class="redirect-btn">
                <i class="fa fa-sign-in"></i> Go to Login Page
            </a>
            <div class="auto-redirect">
                <p>Redirecting automatically in <span id="countdown">5</span> seconds...</p>
            </div>
        </div>
    </div>

    <script>
        // Auto-redirect countdown
        let countdown = 5;
        const countdownElement = document.getElementById('countdown');
        
        const timer = setInterval(function() {
            countdown--;
            countdownElement.textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(timer);
                window.location.href = 'login.php';
            }
        }, 1000);
    </script>
</body>
</html>
