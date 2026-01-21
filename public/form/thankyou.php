<?php
session_start();
require_once 'config.php';

// Check if registration data exists
if (!isset($_SESSION['registration_data'])) {
    header('Location: form.php');
    exit;
}

$data = $_SESSION['registration_data'];
$registrationId = $_SESSION['registration_id'] ?? 'N/A';

// Clear session data after displaying
unset($_SESSION['registration_data']);
unset($_SESSION['registration_id']);

include 'header.php';
?>

<div class="text-center">
    <div class="mb-4">
        <i class="bi bi-check-circle-fill text-success" style="font-size: 80px;"></i>
    </div>
    
    <h1 class="form-title text-success">
        <i class="bi bi-check-circle"></i> Thank You!
    </h1>
    
    <p class="lead mb-4">Your registration has been submitted successfully.</p>
    
    <div class="card mb-4" style="text-align: left;">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-info-circle"></i> Registration Details</h5>
        </div>
        <div class="card-body">
            <p><strong>Registration ID:</strong> <?php echo htmlspecialchars($registrationId); ?></p>
            <p><strong>Name:</strong> <?php echo htmlspecialchars($data['name']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($data['email']); ?></p>
            <p><strong>Mobile:</strong> <?php echo htmlspecialchars($data['mobile']); ?></p>
            <?php if (!empty($data['org'])): ?>
                <p><strong>Organization:</strong> <?php echo htmlspecialchars($data['org']); ?></p>
            <?php endif; ?>
            <?php if (!empty($data['designation'])): ?>
                <p><strong>Designation:</strong> <?php echo htmlspecialchars($data['designation']); ?></p>
            <?php endif; ?>
            <p><strong>Country:</strong> <?php echo htmlspecialchars($data['country']); ?></p>
            <p><strong>Selected Domains:</strong> <?php echo htmlspecialchars($data['domains']); ?></p>
            <p><strong>IP Address:</strong> <?php echo htmlspecialchars($data['user_ip']); ?></p>
        </div>
    </div>
    
    <div class="alert alert-info">
        <i class="bi bi-envelope"></i> A confirmation email will be sent to your registered email address.
    </div>
    
    <div class="mt-4">
        <a href="form.php" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Submit Another Registration
        </a>
    </div>
</div>

<?php include 'footer.php'; ?>
