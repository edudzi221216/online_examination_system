<!DOCTYPE html>
<html>
<head>
    <title>EMS - Examination Management System</title>
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta charset="UTF-8">
    <link href='https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap' rel="stylesheet">
    <link href="assets/plugins/fontawesome/css/font-awesome.css" rel="stylesheet" type="text/css"/>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: #000000;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        
        .login-container {
            position: relative;
            width: 400px;
            height: 500px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            overflow: hidden;
            animation: slideIn 0.8s ease-out;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
        
        .login-form {
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 100%;
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .section-header h2 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .section-header p {
            font-size: 16px;
            opacity: 0.9;
        }
        
        .icon-large {
            font-size: 48px;
            margin-bottom: 20px;
            opacity: 0.9;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .form-group {
            margin-bottom: 20px;
            animation: fadeInUp 0.6s ease-out;
            animation-fill-mode: both;
        }
        
        .form-group:nth-child(1) { animation-delay: 0.2s; }
        .form-group:nth-child(2) { animation-delay: 0.4s; }
        .form-group:nth-child(3) { animation-delay: 0.6s; }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            font-size: 14px;
        }
        
        .form-input {
            width: 100%;
            padding: 12px 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: rgba(255,255,255,0.1);
            color: inherit;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255,255,255,0.2);
        }
        
        .form-input::placeholder {
            color: rgba(255,255,255,0.7);
        }
        
        .btn {
            width: 100%;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            animation: fadeInUp 0.8s ease-out;
            animation-delay: 0.8s;
            animation-fill-mode: both;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            color: white;
            box-shadow: 0 4px 15px rgba(52, 152, 219, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
        }
        
        .forgot-password {
            text-align: center;
            margin-top: 20px;
            animation: fadeInUp 1s ease-out;
            animation-delay: 1s;
            animation-fill-mode: both;
        }
        
        .forgot-password a {
            color: inherit;
            text-decoration: none;
            opacity: 0.8;
            transition: opacity 0.3s ease;
        }
        
        .forgot-password a:hover {
            opacity: 1;
        }
        
        .info-text {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            opacity: 0.8;
            line-height: 1.4;
            animation: fadeInUp 1.2s ease-out;
            animation-delay: 1.2s;
            animation-fill-mode: both;
        }
        
        .user-type-selector {
            display: flex;
            margin-bottom: 20px;
            background: rgba(255,255,255,0.1);
            border-radius: 8px;
            padding: 4px;
            animation: fadeInUp 0.4s ease-out;
            animation-delay: 0.1s;
            animation-fill-mode: both;
        }
        
        .user-type-btn {
            flex: 1;
            padding: 8px 16px;
            background: transparent;
            border: none;
            color: rgba(255,255,255,0.7);
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.3s ease;
            font-size: 14px;
        }
        
        .user-type-btn.active {
            background: rgba(255,255,255,0.2);
            color: white;
        }
        
        .user-type-btn:hover {
            color: white;
        }
        
        @media (max-width: 480px) {
            .login-container {
                width: 90%;
                height: 450px;
            }
            
            .login-form {
                padding: 30px 20px;
            }
            
            .section-header h2 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="login-form">
            <div class="section-header">
                <i class="fa fa-graduation-cap icon-large"></i>
                <h2>EMS Login</h2>
                <p>Examination Management System</p>
            </div>
            
            <div class="user-type-selector">
                <button type="button" class="user-type-btn active" onclick="switchUserType('student')">
                    <i class="fa fa-user-graduate"></i> Student
                </button>
                <button type="button" class="user-type-btn" onclick="switchUserType('staff')">
                    <i class="fa fa-users"></i> Staff
                </button>
            </div>
            
            <form id="loginForm" action="pages/authentication1.php" method="POST">
                <div class="form-group">
                    <label class="form-label" id="userLabel">Student ID or Email</label>
                    <input type="text" class="form-input" placeholder="Enter your student ID or email" 
                           autocomplete="off" name="user" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" class="form-input" placeholder="Enter your password" 
                           name="login" required>
                </div>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-sign-in"></i> <span id="submitText">Student Sign In</span>
                </button>
            </form>
            
            <div class="forgot-password">
                <a href="reset-password.php">Forgot Password?</a>
            </div>
            
            <div class="info-text">
                <i class="fa fa-info-circle"></i> 
                <span id="infoText">Students can take exams and view results</span>
            </div>
        </div>
    </div>

    <script>
        let currentUserType = 'student';
        
        function switchUserType(type) {
            currentUserType = type;
            
            // Update button states
            document.querySelectorAll('.user-type-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Update form action and labels
            const form = document.getElementById('loginForm');
            const userLabel = document.getElementById('userLabel');
            const submitText = document.getElementById('submitText');
            const infoText = document.getElementById('infoText');
            
            if (type === 'student') {
                form.action = 'pages/authentication1.php';
                userLabel.textContent = 'Student ID or Email';
                submitText.textContent = 'Student Sign In';
                infoText.innerHTML = '<i class="fa fa-info-circle"></i> Students can take exams and view results';
            } else {
                form.action = 'pages/authentication.php';
                userLabel.textContent = 'User ID or Email';
                submitText.textContent = 'Staff Sign In';
                infoText.innerHTML = '<i class="fa fa-info-circle"></i> Teachers can create and manage exams<br>Admins have full system access<br>Accountants handle financial records';
            }
            
            // Update input placeholder
            const userInput = form.querySelector('input[name="user"]');
            userInput.placeholder = type === 'student' ? 'Enter your student ID or email' : 'Enter your user ID or email';
        }
        
        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.form-input');
            
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });
            });
        });
    </script>
</body>
</html>
