<?php
/**
 * Template Name: Temporary Login Page
 * 
 * Custom login page that matches the Sarah and Loz theme style
 * This page is part of the temporary authentication system
 */

// If user is already logged in, redirect to home or intended page
if (is_user_logged_in()) {
    $redirect_to = isset($_GET['redirect_to']) ? urldecode($_GET['redirect_to']) : home_url();
    wp_redirect($redirect_to);
    exit;
}

// Get redirect URL
$redirect_to = isset($_GET['redirect_to']) ? $_GET['redirect_to'] : '';
$login_error = isset($_GET['login_error']) ? true : false;
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - <?php bloginfo('name'); ?></title>
    <?php wp_head(); ?>
    
    <style>
        body {
            font-family: 'Harmattan', sans-serif;
            background: linear-gradient(135deg, #f9f9f6 0%, #e8f4f8 100%);
            min-height: 100vh;
            background-image: 
                radial-gradient(#1ddede 1px, transparent 1px),
                radial-gradient(#f8c709 1px, transparent 1px);
            background-size: 50px 50px, 30px 30px;
            background-position: 0 0, 25px 25px;
        }
        
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }
        
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            max-width: 400px;
            width: 100%;
            position: relative;
        }
        
        .login-header {
            background: linear-gradient(135deg, #1ddede 0%, #16a085 100%);
            padding: 2rem;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .login-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: url('<?php echo get_template_directory_uri(); ?>/assets/images/balloons.png') no-repeat center;
            background-size: contain;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }
        
        .login-title {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
            position: relative;
            z-index: 1;
        }
        
        .login-subtitle {
            font-size: 1rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }
        
        .login-form {
            padding: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            color: #2d3748;
            font-weight: 500;
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: #f7fafc;
        }
        
        .form-input:focus {
            outline: none;
            border-color: #1ddede;
            background: white;
            box-shadow: 0 0 0 3px rgba(29, 222, 222, 0.1);
        }
        
        .form-checkbox {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .form-checkbox input[type="checkbox"] {
            width: 1.2rem;
            height: 1.2rem;
            accent-color: #1ddede;
        }
        
        .login-button {
            width: 100%;
            background: linear-gradient(135deg, #1ddede 0%, #16a085 100%);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(29, 222, 222, 0.3);
        }
        
        .login-button:active {
            transform: translateY(0);
        }
        
        .error-message {
            background: linear-gradient(135deg, #fed7d7 0%, #feb2b2 100%);
            color: #c53030;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
            text-align: center;
            font-weight: 500;
            border: 1px solid #fc8181;
        }
        
        .floating-elements {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }
        
        .floating-star {
            position: absolute;
            width: 20px;
            height: 20px;
            background: #f8c709;
            clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
            animation: float-star 4s ease-in-out infinite;
        }
        
        .floating-balloon {
            position: absolute;
            width: 30px;
            height: 40px;
            background: #1ddede;
            border-radius: 50% 50% 50% 50% / 60% 60% 40% 40%;
            animation: float-balloon 5s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(5deg); }
        }
        
        @keyframes float-star {
            0%, 100% { transform: translateY(0) rotate(0deg); opacity: 0.7; }
            50% { transform: translateY(-30px) rotate(180deg); opacity: 1; }
        }
        
        @keyframes float-balloon {
            0%, 100% { transform: translateY(0) rotate(-2deg); }
            50% { transform: translateY(-25px) rotate(2deg); }
        }
        
        /* Mobile responsive */
        @media (max-width: 640px) {
            .login-card {
                margin: 1rem;
                border-radius: 15px;
            }
            
            .login-header {
                padding: 1.5rem;
            }
            
            .login-title {
                font-size: 1.5rem;
            }
            
            .login-form {
                padding: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <!-- Floating decorative elements -->
    <div class="floating-elements">
        <div class="floating-star" style="top: 10%; left: 10%; animation-delay: 0s;"></div>
        <div class="floating-star" style="top: 20%; right: 15%; animation-delay: 1s;"></div>
        <div class="floating-star" style="bottom: 30%; left: 20%; animation-delay: 2s;"></div>
        <div class="floating-star" style="bottom: 20%; right: 10%; animation-delay: 3s;"></div>
        
        <div class="floating-balloon" style="top: 15%; right: 20%; animation-delay: 0.5s;"></div>
        <div class="floating-balloon" style="top: 60%; left: 15%; animation-delay: 1.5s;"></div>
        <div class="floating-balloon" style="bottom: 40%; right: 25%; animation-delay: 2.5s;"></div>
    </div>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1 class="login-title">مرحباً بك!</h1>
                <p class="login-subtitle">يرجى تسجيل الدخول للوصول إلى الموقع</p>
            </div>
            
            <div class="login-form">
                <?php if ($login_error): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        خطأ في تسجيل الدخول. يرجى التحقق من اسم المستخدم وكلمة المرور.
                    </div>
                <?php endif; ?>
                
                <form method="post" action="">
                    <?php wp_nonce_field('temp_login_form', 'temp_login_nonce'); ?>
                    
                    <?php if ($redirect_to): ?>
                        <input type="hidden" name="redirect_to" value="<?php echo esc_attr($redirect_to); ?>">
                    <?php endif; ?>
                    
                    <div class="form-group">
                        <label for="username" class="form-label">
                            <i class="fas fa-user"></i> اسم المستخدم أو البريد الإلكتروني
                        </label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            class="form-input" 
                            required
                            autocomplete="username"
                            placeholder="أدخل اسم المستخدم أو البريد الإلكتروني"
                        >
                    </div>
                    
                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i> كلمة المرور
                        </label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-input" 
                            required
                            autocomplete="current-password"
                            placeholder="أدخل كلمة المرور"
                        >
                    </div>
                    
                    <div class="form-checkbox">
                        <input type="checkbox" id="remember" name="remember" value="1">
                        <label for="remember">تذكرني</label>
                    </div>
                    
                    <button type="submit" class="login-button">
                        <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                    </button>
                </form>
                
                <div style="text-align: center; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid #e2e8f0;">
                    <p style="color: #718096; font-size: 0.9rem;">
                        <i class="fas fa-info-circle"></i>
                        يمكن للمستخدمين المسجلين فقط تسجيل الدخول
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php wp_footer(); ?>
</body>
</html>
