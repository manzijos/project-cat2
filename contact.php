<?php require_once __DIR__ . '/includes/db.php'; include __DIR__ . '/includes/header.php'; ?>
<section class="hero">
    <div class="panel">
        <h2>Contact us</h2>
        <form>
            <div class="form-group"><label>Name</label><input type="text"></div>
            <div class="form-group"><label>Email</label><input type="email"></div>
            <div class="form-group"><label>Message</label><textarea></textarea></div>
            <button class="btn" type="button">Send message</button>
        </form>
    </div>
    <div class="card">
        <h3>Support channels</h3>
        <p>Email: support@azmask.com</p>
        <p>Phone: +254 700 000 000</p>
        <p>Address: Nairobi, Kenya</p>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>