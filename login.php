<?php
require_once 'config.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // In login.php, after successful login verification
        if ($user && password_verify($password, $user['password'])) {
            // Successful login
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_first_name'] = $user['first_name']; // Store first name
            $_SESSION['user_last_name'] = $user['last_name'];   // Store last name
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            
            // Redirect to dashboard or home page
            header('Location: home.php');
            exit();
        }else {
            $error = 'Invalid email or password.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login | BusFinder</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    /* Same styles as register.php */
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
  </style>
</head>
<body>
  <div class="container">
    <div class="auth-card">
      <div class="auth-header">
        <h2><i class="fas fa-sign-in-alt"></i> Welcome Back</h2>
        <p>Login to your BusFinder account</p>
      </div>

      <?php if ($error): ?>
        <div class="error-message"><?php echo $error; ?></div>
      <?php endif; ?>

      <form action="login.php" method="POST">
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
          </div>
        </div>
        
        <div class="mb-3">
          <label for="password" class="form-label">Password</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-lock"></i></span>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
        </div>
        
        <div class="mb-3 form-check">
          <input type="checkbox" class="form-check-input" id="remember">
          <a href="forgot_password.php" class="float-end">Forgot password?</a>
        </div>
        
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-sign-in-alt me-2"></i> Login
        </button>
      </form>
      
      <div class="auth-footer">
        Don't have an account? <a href="register.php">Sign up</a>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>