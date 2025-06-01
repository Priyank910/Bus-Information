<?php
require_once 'config.php';
session_start();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $error = 'Please enter your email address.';
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Generate a simple token (you could also use a random string)
            $token = bin2hex(random_bytes(16));
            $expires = date("Y-m-d H:i:s", time() + 3600); // 1 hour expiration

            // Store token in database
            $stmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE email = ?");
            $stmt->execute([$token, $expires, $email]);

            // Store token in session to verify later
            $_SESSION['reset_token'] = $token;
            $_SESSION['reset_email'] = $email;
            
            // Redirect directly to password reset page
            header("Location: reset_password.php?token=$token");
            exit();
        } else {
            $error = 'No account found with that email address.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forgot Password | BusFinder</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    :root {
      --light-cream: #FCF8E8;
      --mint-green: #D4E2D4;
      --peach: #ECB390;
      --terra-cotta: #DF7861;
    }

    body {
      background-color: var(--light-cream);
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      display: flex;
      align-items: center;
    }

    .auth-card {
      background-color: white;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(223, 120, 97, 0.15);
      border: none;
      padding: 2.5rem;
      width: 100%;
      max-width: 500px;
      margin: 0 auto;
    }

    .auth-header {
      color: var(--terra-cotta);
      text-align: center;
      margin-bottom: 2rem;
      font-weight: 600;
    }

    .auth-header h2 {
      font-weight: 700;
      margin-bottom: 0.5rem;
    }

    .auth-header p {
      color: #666;
    }

    .form-control {
      border-radius: 10px;
      padding: 0.75rem 1rem;
      border: 1px solid #ddd;
    }

    .form-control:focus {
      border-color: var(--peach);
      box-shadow: 0 0 0 0.25rem rgba(236, 179, 144, 0.25);
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--peach), var(--terra-cotta));
      border: none;
      padding: 0.75rem;
      font-weight: 500;
      border-radius: 10px;
      width: 100%;
      margin-top: 1rem;
    }

    .btn-primary:hover {
      background: linear-gradient(135deg, var(--terra-cotta), var(--peach));
    }

    .input-group-text {
      background-color: var(--peach);
      color: white;
      border: none;
    }

    .auth-footer {
      text-align: center;
      margin-top: 1.5rem;
      color: #666;
    }

    .auth-footer a {
      color: var(--terra-cotta);
      font-weight: 500;
      text-decoration: none;
    }

    .error-message {
      color: #dc3545;
      margin-bottom: 1rem;
      text-align: center;
    }
    
    .success-message {
      color: #28a745;
      margin-bottom: 1rem;
      text-align: center;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="auth-card">
      <div class="auth-header">
        <h2><i class="fas fa-key"></i> Forgot Password</h2>
        <p>Enter your email to reset your password</p>
      </div>

      <?php if ($error): ?>
        <div class="error-message"><?php echo $error; ?></div>
      <?php endif; ?>
      
      <?php if ($success): ?>
        <div class="success-message"><?php echo $success; ?></div>
      <?php endif; ?>

      <form action="forgot_password.php" method="POST">
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
        </div>
        
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-paper-plane me-2"></i> Continue to Reset
        </button>
      </form>
      
      <div class="auth-footer">
        Remember your password? <a href="login.php">Login</a>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>