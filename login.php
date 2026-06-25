<?php
require_once __DIR__ . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    if ($username && $password) {
        $db = get_db();
        $stmt = $db->prepare('SELECT * FROM users WHERE username = :username');
        $stmt->execute([':username' => $username]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user'] = $user;
            redirect('dashboard.php');
        } else {
            $_SESSION['error'] = 'Invalid username or password.';
        }
    }
}

include __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div class="panel">
        <h2>Login</h2>
        <?php if (!empty($_SESSION['message'])): ?><p class="badge"><?php echo htmlspecialchars($_SESSION['message']); ?></p><?php unset($_SESSION['message']); endif; ?>
        <?php if (!empty($_SESSION['error'])): ?><p class="badge"><?php echo htmlspecialchars($_SESSION['error']); ?></p><?php unset($_SESSION['error']); endif; ?>
        <form method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <button class="btn" type="submit">Login</button>
        </form>
    </div>
    <div class="card">
        <h3>Demo access</h3>
        <p>Admin username: admin<br>Password: admin123</p>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>