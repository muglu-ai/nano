<?php
session_start();
require_once 'config.php';

// Get user IP address
function getUserIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'];
    }
}

$userIP = getUserIP();
$error = '';
$success = '';

// Get errors from session
if (isset($_SESSION['form_errors']) && !empty($_SESSION['form_errors'])) {
    $error = implode('<br>', $_SESSION['form_errors']);
    unset($_SESSION['form_errors']);
}

// Get error from URL parameter
if (isset($_GET['error'])) {
    $error = htmlspecialchars($_GET['error']);
}

// Restore form data from session
if (isset($_SESSION['form_data'])) {
    $_POST = array_merge($_POST, $_SESSION['form_data']);
    unset($_SESSION['form_data']);
}

// Get countries list
$countries = [
    'India', 'United States', 'United Kingdom', 'Canada', 'Australia', 
    'Germany', 'France', 'Japan', 'China', 'Singapore', 'South Korea',
    'Brazil', 'Mexico', 'Russia', 'Italy', 'Spain', 'Netherlands',
    'Sweden', 'Switzerland', 'Belgium', 'Austria', 'Norway', 'Denmark',
    'Finland', 'Poland', 'Portugal', 'Ireland', 'Greece', 'Czech Republic',
    'Hungary', 'Romania', 'Bulgaria', 'Croatia', 'Slovakia', 'Slovenia',
    'Estonia', 'Latvia', 'Lithuania', 'Luxembourg', 'Malta', 'Cyprus',
    'Other'
];

include 'header.php';
?>

<h1 class="form-title">
    <i class="bi bi-clipboard-check"></i> Domain Registration Form
</h1>

<?php if ($error): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle"></i> <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success">
        <i class="bi bi-check-circle"></i> <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<form action="process.php" method="POST" id="registrationForm">
    
    <!-- Domain Selection -->
    <div class="mb-4">
        <label class="form-label">Select Domains <span class="required">*</span></label>
        <div class="border rounded p-3" style="background-color: #f8f9fa;" id="domains-container">
            <div class="domain-checkbox">
                <input type="checkbox" name="domains[]" value="small satellite" id="domain1">
                <label for="domain1">Small Satellite</label>
            </div>
            <div class="domain-checkbox">
                <input type="checkbox" name="domains[]" value="fly-by-wireless" id="domain2">
                <label for="domain2">Fly-by-Wireless</label>
            </div>
            <div class="domain-checkbox">
                <input type="checkbox" name="domains[]" value="reliability" id="domain3">
                <label for="domain3">Reliability</label>
            </div>
            <div class="domain-checkbox">
                <input type="checkbox" name="domains[]" value="devices" id="domain4">
                <label for="domain4">Devices</label>
            </div>
            <div class="domain-checkbox">
                <input type="checkbox" name="domains[]" value="materials" id="domain5">
                <label for="domain5">Materials</label>
            </div>
            <div class="domain-checkbox">
                <input type="checkbox" name="domains[]" value="energy" id="domain6">
                <label for="domain6">Energy</label>
            </div>
        </div>
        <div id="domain-error" class="error-message" style="display: none;"></div>
        <small class="text-muted">Please select at least one domain</small>
    </div>

    <!-- Mandatory Fields -->
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="name" class="form-label">Name <span class="required">*</span></label>
            <input type="text" class="form-control" id="name" name="name" required 
                   value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
        </div>
        <div class="col-md-6">
            <label for="email" class="form-label">Email <span class="required">*</span></label>
            <input type="email" class="form-control" id="email" name="email" required
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label for="mobile" class="form-label">Mobile <span class="required">*</span></label>
            <input type="tel" class="form-control" id="mobile" name="mobile" required
                   pattern="[0-9]{10,15}" placeholder="10-15 digits"
                   value="<?php echo isset($_POST['mobile']) ? htmlspecialchars($_POST['mobile']) : ''; ?>">
            <small class="text-muted">Enter 10-15 digits</small>
        </div>
        <div class="col-md-6">
            <label for="country" class="form-label">Country <span class="required">*</span></label>
            <select class="form-select" id="country" name="country" required>
                <option value="">-- Select Country --</option>
                <?php foreach ($countries as $country): ?>
                    <option value="<?php echo htmlspecialchars($country); ?>" 
                            <?php echo (isset($_POST['country']) && $_POST['country'] == $country) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($country); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <!-- Non-Mandatory Fields -->
    <div class="row mb-3">
        <div class="col-md-6">
            <label for="org" class="form-label">Organization</label>
            <input type="text" class="form-control" id="org" name="org"
                   value="<?php echo isset($_POST['org']) ? htmlspecialchars($_POST['org']) : ''; ?>">
        </div>
        <div class="col-md-6">
            <label for="designation" class="form-label">Designation</label>
            <input type="text" class="form-control" id="designation" name="designation"
                   value="<?php echo isset($_POST['designation']) ? htmlspecialchars($_POST['designation']) : ''; ?>">
        </div>
    </div>

    <!-- CAPTCHA -->
    <div class="mb-4">
        <label class="form-label">Enter CAPTCHA <span class="required">*</span></label>
        <div class="captcha-container">
            <img src="captcha.php" alt="CAPTCHA" id="captcha-image" class="captcha-image" 
                 style="cursor: pointer;" title="Click to refresh">
            <input type="text" class="form-control" name="captcha" id="captcha" 
                   placeholder="Enter CAPTCHA" required style="max-width: 200px;">
        </div>
        <small class="text-muted">Click on the image to refresh CAPTCHA</small>
    </div>

    <!-- Hidden field for user IP -->
    <input type="hidden" name="user_ip" value="<?php echo htmlspecialchars($userIP); ?>">

    <!-- Submit Button -->
    <div class="text-center">
        <button type="submit" class="btn btn-submit">
            <i class="bi bi-send"></i> Submit Registration
        </button>
    </div>

</form>

<?php include 'footer.php'; ?>
