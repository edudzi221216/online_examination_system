<!--Accountant Login page-->
<!DOCTYPE html>
<html>
    
<head>
        <title>Accountant Login</title>
        <meta content="width=device-width, initial-scale=1" name="viewport"/>
        <meta charset="UTF-8">
        <link href='https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap' rel='stylesheet'>
        <link href="../assets/plugins/fontawesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
        <link href="../assets/css/modern-design-system.css" rel="stylesheet" type="text/css"/>
</head>

<body>
    <div class="modern-login-container">
        <!-- Floating Elements -->
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        <div class="floating-element"></div>
        
        <div class="modern-login-card">
            <div class="modern-login-header">
                <div class="modern-login-logo">
                    <i class="fa fa-calculator"></i>
                </div>
                <h1 class="modern-login-title">Accountant Portal</h1>
                <p class="modern-login-subtitle">Financial Management Access</p>
            </div>
            
            <form action="../pages/authentication.php" method="POST">
                <div class="modern-form-group">
                    <label class="modern-form-label">Accountant ID or Email</label>
                    <input type="text" class="modern-form-input" placeholder="Enter your accountant ID or email" 
                           autocomplete="off" name="user" required>
                </div>
                
                <div class="modern-form-group">
                    <label class="modern-form-label">Password</label>
                    <input type="password" class="modern-form-input" placeholder="Enter your password" 
                           name="login" required>
                </div>
                
                <button type="submit" class="modern-btn modern-btn-primary modern-btn-block">
                    <i class="fa fa-sign-in"></i> Sign In
                </button>
            </form>
            
            <div class="modern-login-footer">
                <a href="../login.php">Admin/Teacher Login →</a>
                <br><br>
                <a href="../index.php">Student Login →</a>
            </div>
        </div>
    </div>

    <script src="../assets/plugins/jquery/jquery-2.1.4.min.js"></script>
</body>
</html>
