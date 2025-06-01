<?php
require_once 'config.php';
session_start();

$error = '';
$success = '';

// Check if token is valid
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Verify token matches session token
    if (!isset($_SESSION['reset_token']) || $token !== $_SESSION['reset_token']) {
        $error = 'Invalid or expired password reset link.';
    }
} else {
    $error = 'No password reset token provided.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error) {
    $new_password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    if (empty($new_password)) {
        $error = 'Please enter a new password.';
    } elseif (strlen($new_password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        // Update password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE email = ?");
        $stmt->execute([$hashed_password, $_SESSION['reset_email']]);
        
        // Clear reset session
        unset($_SESSION['reset_token']);
        unset($_SESSION['reset_email']);
        
        $success = 'Your password has been updated successfully. You can now <a href="login.php">login</a> with your new password.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password | BusFinder</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    /* Same styles as forgot_password.php */
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
        <h2><i class="fas fa-key"></i> Reset Password</h2>
        <p>Enter your new password</p>
      </div>

      <?php if ($error): ?>
        <div class="error-message"><?php echo $error; ?></div>
      <?php elseif ($success): ?>
        <div class="success-message"><?php echo $success; ?></div>
      <?php else: ?>
        <form action="reset_password.php?token=<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>" method="POST">
          <div class="mb-3">
            <label for="password" class="form-label">New Password</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-lock"></i></span>
              <input type="password" class="form-control" id="password" name="password" required minlength="8">
            </div>
          </div>
          
          <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm New Password</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-lock"></i></span>
              <input type="password" class="form-control" id="confirm_password" name="confirm_password" required minlength="8">
            </div>
          </div>
          
          <button type="submit" class="btn btn-primary">
            <i class="fas fa-save me-2"></i> Update Password
          </button>
        </form>
      <?php endif; ?>
      
      <div class="auth-footer">
        Remember your password? <a href="login.php">Login</a>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>