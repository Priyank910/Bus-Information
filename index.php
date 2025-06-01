<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $phone = trim($_POST['phone']);

    // Validate inputs
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } else {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $error = 'Email already registered.';
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, phone) VALUES (?, ?, ?, ?, ?)");
            if ($stmt->execute([$firstName, $lastName, $email, $hashedPassword, $phone])) {
                $success = 'Registration successful! You can now login.';
                // Clear form
                $firstName = $lastName = $email = $phone = '';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register | BusFinder</title>
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
        <h2><i class="fas fa-user-plus"></i> Create Account</h2>
        <p>Join BusFinder to book your bus tickets</p>
      </div>

      <?php if ($error): ?>
        <div class="error-message"><?php echo $error; ?></div>
      <?php endif; ?>

      <?php if ($success): ?>
        <div class="success-message"><?php echo $success; ?></div>
      <?php endif; ?>

      <form action="index.php" method="POST">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo isset($firstName) ? htmlspecialchars($firstName) : ''; ?>" required>
          </div>
          <div class="col-md-6">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo isset($lastName) ? htmlspecialchars($lastName) : ''; ?>" required>
          </div>
          
          <div class="col-12">
            <label for="email" class="form-label">Email</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-envelope"></i></span>
              <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
            </div>
          </div>
          
          <div class="col-12">
            <label for="password" class="form-label">Password</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-lock"></i></span>
              <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <small class="text-muted">Minimum 8 characters</small>
          </div>
          
          <div class="col-12">
            <label for="phone" class="form-label">Phone Number (Optional)</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fas fa-phone"></i></span>
              <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo isset($phone) ? htmlspecialchars($phone) : ''; ?>">
            </div>
          </div>
          
          <div class="col-12">
            <button type="submit" class="btn btn-primary">
              <i class="fas fa-user-plus me-2"></i> Register
            </button>
          </div>
        </div>
      </form>
      
      <div class="auth-footer">
        Already have an account? <a href="login.php">Sign in</a>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>