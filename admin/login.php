<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start the session first before any output
session_start();

// admin/login.php
require_once '../includes/db.php';

// Debug session status
// echo "<div style='background: #f8f9fa; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd;'>";
// echo "Session status: " . session_status() . " (2 means active)<br>";
// echo "Session ID: " . session_id() . "<br>";
// echo "</div>";

// Check if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    echo "Already logged in, redirecting...";
    header('Location: /shoe_store/admin/index.php');
    exit;
}

$error = null;

// Process login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug POST data
    echo "<div style='background: #f8f9fa; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd;'>";
    echo "Form submitted:<br>";
    echo "POST data received: " . (!empty($_POST) ? 'Yes' : 'No') . "<br>";
    echo "POST contents: <pre>" . print_r($_POST, true) . "</pre>";
    echo "</div>";
    
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    // Debug credentials (only for testing, remove in production)
    echo "<div style='background: #f8f9fa; padding: 10px; margin-bottom: 10px; border: 1px solid #ddd;'>";
    echo "Username entered: " . htmlspecialchars($username) . "<br>";
    echo "Password entered: " . (empty($password) ? "No password" : "Password provided") . "<br>";
    echo "Authentication result: ";
    
    // Simple authentication check
    if ($username === 'admin' && $password === 'admin123') {
        echo "<span style='color: green'>Success!</span>";
        $_SESSION['admin_logged_in'] = true;
        echo "<br>SESSION set: " . (isset($_SESSION['admin_logged_in']) ? 'Yes' : 'No');
        echo "<br>Redirecting to admin panel...";
        echo "</div>";
        
        // Uncomment when debugging is complete
        header('Location: /shoe_store/admin/index.php');
        exit;
    } else {
        echo "<span style='color: red'>Failed!</span>";
        echo "<br>Expected: username='admin', password='admin123'";
        echo "<br>Received: username='" . htmlspecialchars($username) . "', password=[hidden]";
        echo "</div>";
        
        $error = "Invalid username or password";
    }
}

// Variable to prevent duplicate session start in header.php
$disable_session_start = true;

// Include header
include '../includes/header.php';
?>

<div class="login-container">
    <div class="login-form-wrapper">
        <div class="login-header">
            <h2>Admin Login</h2>
            <p>Enter your credentials to access the admin panel</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>
        
        <!-- Use a simplified form for testing -->
        <form action="/shoe_store/admin/login.php" method="POST" class="login-form">
            <div class="form-group">
                <label for="username">Username</label>
                <div class="input-with-icon">
                    <i class="icon-user"></i>
                    <input type="text" id="username" name="username" placeholder="Enter username" value="admin" required>
                </div>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-with-icon">
                    <i class="icon-lock"></i>
                    <input type="password" id="password" name="password" placeholder="Enter password" value="admin123" required>
                </div>
            </div>
            
            <div class="form-group remember-me">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember me</label>
            </div>
            
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>
        
        <!-- Alternative basic form for troubleshooting -->
        <!-- <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
            <h3>Alternative Login Form</h3>
            <form action="/shoe_store/admin/login.php" method="POST">
                <input type="text" name="username" value="admin" placeholder="Username">
                <input type="password" name="password" value="admin123" placeholder="Password">
                <button type="submit">Login</button>
            </form>
        </div> -->
        
        <div class="login-footer">
            <p>Shoe Store Admin Panel &copy; <?php echo date('Y'); ?></p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>