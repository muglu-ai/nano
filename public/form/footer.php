        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Refresh CAPTCHA on click
        document.addEventListener('DOMContentLoaded', function() {
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
