        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- intl-tel-input JS -->
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js"></script>
    <script>
        // Initialize intl-tel-input for mobile number
        document.addEventListener('DOMContentLoaded', function() {
            const mobileInput = document.getElementById('mobile');
            const mobileCountryCodeInput = document.getElementById('mobile_country_code');
            let iti = null;
            
            if (mobileInput) {
                // Initialize intl-tel-input
                iti = window.intlTelInput(mobileInput, {
                    initialCountry: 'in',
                    preferredCountries: ['in', 'us', 'gb'],
                    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js",
                    separateDialCode: true,
                    nationalMode: false,
                    autoPlaceholder: 'off'
                });
                
                // Set placeholder to empty
                mobileInput.placeholder = '';
                
                // Update hidden country code field when country changes
                mobileInput.addEventListener('countrychange', function() {
                    const countryData = iti.getSelectedCountryData();
                    if (mobileCountryCodeInput) {
                        mobileCountryCodeInput.value = countryData.dialCode;
                    }
                });
                
                // Set initial country code
                const initialCountryData = iti.getSelectedCountryData();
                if (mobileCountryCodeInput) {
                    mobileCountryCodeInput.value = initialCountryData.dialCode;
                }
                
                // Update country code on blur
                mobileInput.addEventListener('blur', function() {
                    const countryData = iti.getSelectedCountryData();
                    if (mobileCountryCodeInput) {
                        mobileCountryCodeInput.value = countryData.dialCode;
                    }
                });
            }
            
            // Refresh CAPTCHA on click
            const captchaImage = document.getElementById('captcha-image');
            if (captchaImage) {
                captchaImage.addEventListener('click', function() {
                    this.src = 'captcha.php?' + new Date().getTime();
                });
            }
            
            // Form validation
            const form = document.getElementById('registrationForm');
            const domainError = document.getElementById('domain-error');
            
            if (form) {
                // Validate domains on submit
                form.addEventListener('submit', function(e) {
                    const domains = document.querySelectorAll('input[name="domains[]"]:checked');
                    if (domains.length === 0) {
                        e.preventDefault();
                        if (domainError) {
                            domainError.textContent = 'Please select at least one domain';
                            domainError.style.display = 'block';
                        } else {
                            alert('Please select at least one domain');
                        }
                        return false;
                    } else {
                        if (domainError) {
                            domainError.style.display = 'none';
                        }
                    }
                    
                    // Validate mobile number using intl-tel-input
                    if (iti && mobileInput) {
                        if (!iti.isValidNumber()) {
                            e.preventDefault();
                            alert('Please enter a valid mobile number');
                            return false;
                        }
                    }
                });
                
                // Clear domain error when user selects a domain
                const domainCheckboxes = document.querySelectorAll('input[name="domains[]"]');
                domainCheckboxes.forEach(function(checkbox) {
                    checkbox.addEventListener('change', function() {
                        const checked = document.querySelectorAll('input[name="domains[]"]:checked');
                        if (checked.length > 0 && domainError) {
                            domainError.style.display = 'none';
                        }
                    });
                });
            }
        });
    </script>
</body>
</html>
