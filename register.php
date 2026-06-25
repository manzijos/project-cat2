<?php
require_once __DIR__ . '/includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $fullName = trim($_POST['full_name'] ?? '');
    $role = trim($_POST['role'] ?? 'customer');
    $location = trim($_POST['location'] ?? '');

    if ($username && $password && $fullName) {
        $db = get_db();
        $stmt = $db->prepare('INSERT INTO users (username, password_hash, role, full_name, location) VALUES (:username, :password_hash, :role, :full_name, :location)');
        $stmt->execute([
            ':username' => $username,
            ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
            ':role' => $role,
            ':full_name' => $fullName,
            ':location' => $location
        ]);
        $_SESSION['message'] = 'Account created successfully. Please log in.';
        redirect('login.php');
    } else {
        $_SESSION['error'] = 'Please fill in all required fields.';
    }
}

include __DIR__ . '/includes/header.php';
?>
<section class="hero">
    <div class="panel">
        <h2>Create account</h2>
        <form method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Full name</label>
                <input type="text" name="full_name" required>
            </div>
            <div class="form-group">
                <label>Role</label>
                <select name="role">
                    <option value="customer">Customer</option>
                    <option value="seller">Seller</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location">
            </div>
            <button class="btn" type="submit">Register</button>
        </form>
    </div>
    <div class="card">
        <h3>Why join?</h3>
        <p>Customers can order and review products, sellers can upload products and documents, and admins can manage the platform.</p>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>