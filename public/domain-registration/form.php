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

$countries = array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote d'Ivoire", "Croatia (Hrvatska)", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "France Metropolitan", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard and Mc Donald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran (Islamic Republic of)", "Iraq", "Ireland", "Israel", "Italy", "Jamaica", "Japan", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People's Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao, People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "Rwanda", "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Seychelles", "Sierra Leone", "Singapore", "Slovakia (Slovak Republic)", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "St. Helena", "St. Pierre and Miquelon", "Sudan", "Suriname", "Svalbard and Jan Mayen Islands", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Vietnam", "Virgin Islands (British)", "Virgin Islands (U.S.)", "Wallis and Futuna Islands", "Western Sahara", "Yemen", "Yugoslavia", "Zambia", "Zimbabwe");


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

include 'header.php';
?>

<h1 class="form-title">
    <i class="bi bi-handshake"></i> Partner with US
</h1>

<?php if ($error): ?>
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-triangle"></i> <?php echo $error; ?>
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
                <input type="checkbox" name="domains[]" value="small satellite" id="domain1"
                       <?php echo (isset($_POST['domains']) && in_array('small satellite', $_POST['domains'])) ? 'checked' : ''; ?>>
                <label for="domain1">Small Satellite</label>
            </div>
            <div class="domain-checkbox">
                <input type="checkbox" name="domains[]" value="fly-by-wireless" id="domain2"
                       <?php echo (isset($_POST['domains']) && in_array('fly-by-wireless', $_POST['domains'])) ? 'checked' : ''; ?>>
                <label for="domain2">Fly-by-Wireless</label>
            </div>
            <div class="domain-checkbox">
                <input type="checkbox" name="domains[]" value="reliability" id="domain3"
                       <?php echo (isset($_POST['domains']) && in_array('reliability', $_POST['domains'])) ? 'checked' : ''; ?>>
                <label for="domain3">Reliability</label>
            </div>
            <div class="domain-checkbox">
                <input type="checkbox" name="domains[]" value="devices" id="domain4"
                       <?php echo (isset($_POST['domains']) && in_array('devices', $_POST['domains'])) ? 'checked' : ''; ?>>
                <label for="domain4">Devices</label>
            </div>
            <div class="domain-checkbox">
                <input type="checkbox" name="domains[]" value="materials" id="domain5"
                       <?php echo (isset($_POST['domains']) && in_array('materials', $_POST['domains'])) ? 'checked' : ''; ?>>
                <label for="domain5">Materials</label>
            </div>
            <div class="domain-checkbox">
                <input type="checkbox" name="domains[]" value="energy" id="domain6"
                       <?php echo (isset($_POST['domains']) && in_array('energy', $_POST['domains'])) ? 'checked' : ''; ?>>
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
                   value="<?php echo isset($_POST['mobile']) ? htmlspecialchars($_POST['mobile']) : ''; ?>">
            <input type="hidden" id="mobile_country_code" name="mobile_country_code" value="">
            <small class="text-muted">Select country and enter mobile number</small>
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
                   placeholder="Enter CAPTCHA" required style="max-width: 200px;"
                   value="<?php echo isset($_POST['captcha']) ? htmlspecialchars($_POST['captcha']) : ''; ?>">
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
