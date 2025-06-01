<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$host = "localhost";
$user = "root";
$password = ""; // set this if your MySQL user has a password
$dbname = "bus_booking";


// Connect to the database
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$success_message = '';
$error_message = '';

// Fetch user details
$userId = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Prepare name for navbar display
$userName = $user['first_name'] . " " . $user['last_name'];
$firstName = $user['first_name'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    
    // Basic validation
    if (empty($first_name)) {
        $error_message = "First name is required";
    } elseif (empty($last_name)) {
        $error_message = "Last name is required";
    } elseif (empty($email)) {
        $error_message = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format";
    } elseif (empty($phone)) {
        $error_message = "Phone number is required";
    } else {
        // Check if email already exists for another user
        $check_email = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $check_email->bind_param("si", $email, $userId);
        $check_email->execute();
        $check_email->store_result();
        
        if ($check_email->num_rows > 0) {
            $error_message = "Email already in use by another account";
        } else {
            // Update user details in database
            $update_sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, updated_at = NOW() WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ssssi", $first_name, $last_name, $email, $phone, $userId);
            
            if ($update_stmt->execute()) {
                $success_message = "Profile updated successfully!";
                // Refresh user data
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                $userName = $user['first_name'] . " " . $user['last_name'];
                $firstName = $user['first_name'];
            } else {
                $error_message = "Error updating profile: " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Profile - BusFinder</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    :root {
      --light-cream: #FCF8E8;
      --mint-green: #D4E2D4;
      --peach: #ECB390;
      --terra-cotta: #DF7861;
      --dark-green: #5C7A67;
      
      /* Light theme colors */
      --bg-color: var(--light-cream);
      --card-bg: white;
      --text-color: #333;
      --text-muted: #666;
      --header-color: var(--terra-cotta);
      --nav-bg: linear-gradient(90deg, var(--peach), var(--terra-cotta));
      --footer-bg: var(--mint-green);
      --input-bg: white;
      --input-border: #ddd;
    }

    /* Dark theme colors */
    .dark-theme {
      --bg-color: #1a1a1a;
      --card-bg: #2d2d2d;
      --text-color: #f0f0f0;
      --text-muted: #b0b0b0;
      --header-color: #ECB390;
      --nav-bg: linear-gradient(90deg, #3a3a3a, #4a4a4a);
      --footer-bg: #2a3a2d;
      --input-bg: #3a3a3a;
      --input-border: #4a4a4a;
    }

    body {
      background-color: var(--bg-color);
      color: var(--text-color);
      font-family: 'Poppins', sans-serif;
      min-height: 100vh;
      padding-top: 70px;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    /* Navigation Bar */
    .navbar {
      background: var(--nav-bg);
      box-shadow: 0 4px 20px rgba(223, 120, 97, 0.3);
      padding: 0.8rem 2rem;
    }

    .navbar-brand {
      font-weight: 700;
      font-size: 1.8rem;
      color: white !important;
      display: flex;
      align-items: center;
    }

    .navbar-brand i {
      margin-right: 10px;
      font-size: 1.5rem;
    }

    .nav-link {
      color: rgba(255, 255, 255, 0.9) !important;
      font-weight: 500;
      margin: 0 8px;
      padding: 8px 15px !important;
      border-radius: 50px;
      transition: all 0.3s ease;
      position: relative;
    }

    .nav-link:hover, .nav-link.active {
      color: white !important;
      background-color: rgba(255, 255, 255, 0.15);
      transform: translateY(-2px);
    }

    .nav-link.active::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 20px;
      height: 3px;
      background-color: white;
      border-radius: 3px;
    }

    .navbar-toggler {
      border: none;
      color: white !important;
    }

    .navbar-toggler:focus {
      box-shadow: none;
    }

    .search-card {
      background-color: var(--card-bg);
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(223, 120, 97, 0.15);
      border: none;
      padding: 2.5rem;
      margin-bottom: 2rem;
      transition: transform 0.3s ease;
    }

    .search-card:hover {
      transform: translateY(-5px);
    }

    .search-header {
      color: var(--header-color);
      text-align: center;
      margin-bottom: 2.5rem;
      font-weight: 600;
      position: relative;
      padding-bottom: 1rem;
    }

    .search-header::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 100px;
      height: 4px;
      background: linear-gradient(90deg, var(--peach), var(--terra-cotta));
      border-radius: 2px;
    }

    .search-header h1 {
      font-weight: 700;
      font-size: 2.5rem;
      margin-bottom: 0.5rem;
    }

    .search-header p {
      font-size: 1.1rem;
      color: var(--text-muted);
    }

    .search-header i {
      margin-right: 12px;
      background: linear-gradient(135deg, var(--peach), var(--terra-cotta));
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .btn-primary {
      background: linear-gradient(135deg, var(--peach), var(--terra-cotta));
      border: none;
      padding: 0.75rem 1.75rem;
      font-weight: 500;
      border-radius: 50px;
      box-shadow: 0 4px 15px rgba(223, 120, 97, 0.3);
      transition: all 0.3s ease;
      height: 100%;
    }

    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(223, 120, 97, 0.4);
    }

    .form-control,
    .form-select {
      background-color: var(--input-bg);
      color: var(--text-color);
      border-radius: 10px;
      padding: 0.75rem 1rem;
      border: 1px solid var(--input-border);
    }

    .form-control:focus,
    .form-select:focus {
      background-color: var(--input-bg);
      color: var(--text-color);
      border-color: var(--peach);
      box-shadow: 0 0 0 0.25rem rgba(236, 179, 144, 0.25);
    }

    .input-group-text {
      background-color: var(--peach);
      color: white;
      border: none;
      border-radius: 10px 0 0 10px !important;
    }

    .results-table {
      background-color: var(--card-bg);
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      margin-bottom: 3rem;
    }

    .table {
      color: var(--text-color);
    }

    .table thead {
      background: linear-gradient(90deg, var(--peach), var(--terra-cotta));
      color: white;
    }

    .table th {
      font-weight: 600;
      padding: 1.25rem;
      border-bottom: none;
    }

    .table td {
      padding: 1.25rem;
      vertical-align: middle;
      border-top: 1px solid rgba(0, 0, 0, 0.05);
    }

    .table-hover tbody tr:hover {
      background-color: rgba(212, 226, 212, 0.3);
      transform: scale(1.01);
      transition: all 0.2s ease;
    }

    .btn-info {
      background-color: var(--peach);
      border-color: var(--peach);
      color: white;
      border-radius: 8px;
      padding: 0.5rem 1rem;
      transition: all 0.3s ease;
    }

    .btn-info:hover {
      background-color: #e0a07d;
      border-color: #e0a07d;
      transform: translateY(-2px);
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .alert-warning {
      background-color: var(--mint-green);
      border-color: var(--peach);
      color: var(--text-color);
      border-radius: 15px;
      padding: 1.5rem;
      text-align: center;
      font-size: 1.1rem;
    }

    .route-direction {
      font-weight: 500;
      color: var(--terra-cotta);
      display: flex;
      align-items: center;
    }

    .route-direction i {
      margin: 0 10px;
      color: var(--peach);
    }

    .bus-name {
      font-weight: 600;
      color: var(--text-color);
    }

    .price-badge {
      background-color: var(--mint-green);
      color: var(--terra-cotta);
      padding: 0.5rem 1rem;
      border-radius: 50px;
      font-weight: 600;
    }

    .date-badge {
      background-color: var(--input-bg);
      color: var(--text-color);
      padding: 0.5rem 1rem;
      border-radius: 50px;
      font-weight: 500;
      font-size: 0.9rem;
    }

    .time-badge {
      background-color: rgba(223, 120, 97, 0.1);
      color: var(--terra-cotta);
      padding: 0.5rem 1rem;
      border-radius: 50px;
      font-weight: 500;
    }

    .empty-state {
      text-align: center;
      padding: 4rem 0;
    }

    .empty-state i {
      font-size: 5rem;
      color: var(--peach);
      margin-bottom: 1.5rem;
      opacity: 0.7;
    }

    .empty-state h3 {
      color: var(--terra-cotta);
      margin-bottom: 1rem;
    }

    .empty-state p {
      color: var(--text-muted);
      max-width: 500px;
      margin: 0 auto 2rem;
    }

    .floating-bus {
      position: absolute;
      font-size: 3rem;
      color: rgba(223, 120, 97, 0.1);
      z-index: -1;
    }

    .floating-bus-1 {
      top: 10%;
      left: 5%;
      animation: float 6s ease-in-out infinite;
    }

    .floating-bus-2 {
      bottom: 15%;
      right: 5%;
      animation: float 8s ease-in-out infinite 2s;
    }

    @keyframes float {
      0% {
        transform: translateY(0) rotate(0deg);
      }

      50% {
        transform: translateY(-20px) rotate(5deg);
      }

      100% {
        transform: translateY(0) rotate(0deg);
      }
    }

    .switch-icon {
      cursor: pointer;
      color: var(--peach);
      transition: all 0.3s ease;
      font-size: 1.2rem;
      margin: 0 10px;
    }

    .switch-icon:hover {
      color: var(--terra-cotta);
      transform: rotate(180deg) scale(1.2);
    }

    .form-label {
      font-weight: 500;
      color: var(--text-color);
      margin-bottom: 0.5rem;
    }

    footer {
      background-color: var(--footer-bg);
      padding: 1.5rem 0;
      text-align: center;
      color: var(--text-color);
      border-radius: 20px 20px 0 0;
      margin-top: 3rem;
    }

    footer p {
      margin-bottom: 0;
    }

    /* Dropdown menu styling */
    .dropdown-menu {
      background-color: var(--card-bg);
      border-radius: 15px;
      border: none;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      padding: 10px 0;
      margin-top: 10px;
    }

    .dropdown-item {
      padding: 8px 20px;
      font-weight: 500;
      color: var(--text-color);
      transition: all 0.2s ease;
    }

    .dropdown-item:hover {
      background-color: rgba(223, 120, 97, 0.1);
      color: var(--terra-cotta);
      transform: translateX(5px);
    }

    .dropdown-divider {
      border-color: rgba(223, 120, 97, 0.1);
    }

    /* User profile in navbar */
    .user-profile {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background-color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      color: var(--terra-cotta);
      font-weight: 600;
      margin-left: 10px;
    }

    /* Theme toggle button */
    .theme-toggle {
      background: none;
      border: none;
      color: light;
      font-size: 1.2rem;
      cursor: pointer;
      margin-right: 15px;
      transition: all 0.3s ease;
    }

    .theme-toggle:hover {
      transform: scale(1.1);
    }
  </style>
</head>
<body>

  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
    <div class="container">
      <a class="navbar-brand" href="home.php">
        <i class="fas fa-bus"></i> BusFinder
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link" href="http://localhost/GSRTC/search.php"><i class="fas fa-search me-1"></i> Search</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="http://localhost/GSRTC/admin.php"><i class="fas fa-bus-alt me-1"></i> Add Buses</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
              <i class="fas fa-cog me-1"></i> Settings
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user-cog me-2 m-1"></i>Account Settings</a></li>
              <li><a class="dropdown-item" href="#" id="theme-toggle"><i class="fas fa-moon me-2"></i>Theme</a></li>
            </ul>
          </li>
        </ul>
        <ul class="navbar-nav ms-auto">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                <span><?php echo htmlspecialchars($userName); ?></span>
                <div class="user-profile">
                    <?php echo strtoupper(substr($firstName, 0, 1)); ?>
                </div>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
              <li><a class="dropdown-item" href="profile.php"><i class="fas fa-user me-2"></i>Profile</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container mt-5">
    <div class="search-card mx-auto" style="max-width: 800px;">
      <div class="search-header">
        <h1><i class="fas fa-user-circle"></i> Your Profile</h1>
        <p>Here are your account details</p>
      </div>
      
      <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
      <?php endif; ?>
      
      <?php if ($error_message): ?>
        <div class="alert alert-danger"><?php echo $error_message; ?></div>
      <?php endif; ?>
      
      <form method="POST" action="profile.php">
        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">First Name</label>
            <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Last Name</label>
            <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
          </div>
        </div>

        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Phone</label>
          <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
        </div>

        <div class="row mb-3">
          <div class="col-md-6">
            <label class="form-label">Created At</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['created_at']); ?>" readonly>
          </div>
          <div class="col-md-6">
            <label class="form-label">Last Updated</label>
            <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['updated_at']); ?>" readonly>
          </div>
        </div>
        
        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
          <button type="submit" class="btn btn-primary edit-btn">
            <i class="fas fa-save me-1"></i> Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>

  <footer>
    <p>&copy; <?php echo date('Y'); ?> BusFinder. All rights reserved.</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Theme toggle script
    const toggleBtn = document.getElementById('theme-toggle');
    toggleBtn?.addEventListener('click', () => {
      document.body.classList.toggle('dark-theme');
    });
  </script>
</body>
</html>
