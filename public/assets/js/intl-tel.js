function cinitIntlTelInput() {
  document.querySelectorAll(".phone-input").forEach((input, index) => {
    // Initialize the intl-tel-input library
    const iti = window.intlTelInput(input, {
      initialCountry: "auto",
      nationalMode: false,
      formatOnDisplay: true,
      autoPlaceholder: "off",
      separateDialCode: true, // Changed to true to show dial code
      preferredCountries: ["IN", "US", "GB"], // Add preferred countries
      
      geoIpLookup: function (callback) {
        fetch("https://ipapi.co/json")
          .then(res => res.json())
          .then(data => callback(data.country_code || "IN"))
          .catch(() => callback("IN")); // Fallback to India on error
      },
      utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js"
    });

    // Store the iti instance
    input._iti = iti;

    // Add validation and formatting on form submission
    input.closest('form').addEventListener('submit', function(e) {
      e.preventDefault();
      
      if (iti.isValidNumber()) {
        // Get the full international number
        const fullNumber = iti.getNumber(); // This gets number with country code
        
        // Update the input value with the full international number
        input.value = fullNumber;
        
        // You can also get additional information if needed
        const countryData = {
          countryCode: iti.getSelectedCountryData().iso2,
          dialCode: iti.getSelectedCountryData().dialCode,
          fullNumber: fullNumber

        };
        
        console.log('Phone number details:', countryData);
        //preeveent form submission to handle validation
        alert('Phone number is valid: ' + fullNumber);
        e.preventDefault();
        this.submit(); // Submit the form
      } else {
        // Show error if number is invalid
        const errorCode = iti.getValidationError();
        alert('Please enter a valid phone number');
        e.preventDefault();
      }
    });

    // Handle input formatting
    input.addEventListener('input', function() {
      let val = this.value;
      if (val.startsWith("0")) {
        this.value = val.replace(/^0+/, '');
      }
    });

    // Clear placeholder on load
    window.addEventListener('load', () => {
      input.placeholder = '';
    });
  });

  document.querySelectorAll('.attendee-next-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const index = this.getAttribute('data-index');
      const input = document.querySelector(`.phone-input[data-index="${index}"]`);
      if (input && input._iti) {
        const fullNumber = input._iti.getNumber();
        console.log('Phone number with country code:', fullNumber);
      } else {
        console.log('Phone input not found for index:', index);
      }
    });
  });
}