// visitor-form.js

let attendeeCount = document.querySelectorAll('[name^="attendees["][name$="[first_name]"]').length;

function validatePurposeRequired(idx, selected) {
    const checkboxes = document.querySelectorAll(`[name="attendees[${idx}][purpose][]"]`);

    // Remove default 'required' attributes
    checkboxes.forEach(cb => cb.required = false);

    const form = checkboxes[0]?.closest('form');

    // Clean up previous submit listener if any
    if (form && form._purposeHandlerAdded) {
        form.removeEventListener('submit', form._purposeHandlerFn);
        form._purposeHandlerAdded = false;
    }

    if (selected === 'Exhibitor' || selected === 'Industry') {
        const purposeHandler = function (e) {
            // Clear all previous validity
            checkboxes.forEach(cb => cb.setCustomValidity(''));

            const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
            if (!anyChecked) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Select Purpose of Visit',
                    text: 'Please select at least one purpose of visit.',
                    confirmButtonText: 'OK'
                });

                checkboxes[0].setCustomValidity('Please select at least one purpose.');
                checkboxes[0].reportValidity();
                e.preventDefault();
            }
        };

        if (form) {
            form.addEventListener('submit', purposeHandler);
            form._purposeHandlerAdded = true;
            form._purposeHandlerFn = purposeHandler;
        }

        // ✅ This is where you add the change listener
        checkboxes.forEach(cb => {
            cb.addEventListener('change', () => {
                cb.setCustomValidity('');
            });
        });
    }
}


function validateEventDaysRequired(idx) {
    const checkboxes = document.querySelectorAll(`[name="attendees[${idx}][event_days][]"]`);
    const form = checkboxes[0]?.closest('form');

    if (!form) return;

    // Prevent multiple event handlers
    if (form._eventDaysHandlerAdded) {
        form.removeEventListener('submit', form._eventDaysHandlerFn);
        form._eventDaysHandlerAdded = false;
    }

    const handler = function (e) {
        checkboxes.forEach(cb => cb.setCustomValidity('')); // Reset validity

        const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
        if (!anyChecked) {
            Swal.fire({
                icon: 'warning',
                title: 'Select Event Day(s)',
                text: 'Please select at least one event day to attend.',
                confirmButtonText: 'OK'
            });

            checkboxes[0].setCustomValidity('Please select at least one event day.');
            checkboxes[0].reportValidity();
            e.preventDefault();
        }
    };

    form.addEventListener('submit', handler);
    form._eventDaysHandlerAdded = true;
    form._eventDaysHandlerFn = handler;

}


function showError(message) {
    Swal.fire({ icon: 'error', title: 'Oops...', text: message });
}

if (typeof validationErrors !== 'undefined' && Array.isArray(validationErrors)) {
    validationErrors.forEach(error => showError(error));
}

const reloadCaptcha = document.getElementById('reload');
if (reloadCaptcha) {
    reloadCaptcha.addEventListener('click', function () {
        const captchaImg = document.getElementById('captcha-img');
        captchaImg.innerHTML = '<div class="spinner-border text-secondary" role="status"></div>';
        fetch('/reload-captcha')
            .then(response => response.json())
            .then(data => {
                captchaImg.innerHTML = data.captcha;
                document.getElementById('captcha').focus();
            });
    });
}

function sendOtp(btn, index) {
    const input = btn.closest('.input-group').querySelector('input[type="email"]');
    const email = input.value.trim();
    if (!email) {
        Swal.fire({ icon: 'warning', title: 'Please enter an email address first.' });
        return;
    }

    //validate the email format
    // Improved email regex for better validation
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!emailPattern.test(email)) {
        Swal.fire({ icon: 'warning', title: 'Invalid email format.', text: 'Please enter a valid email address.' });
        return;
    }

    btn.disabled = true;
    btn.innerText = "Sending...";

    Swal.fire({
        title: 'Sending OTP...',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    axios.post('/otp/send', { email })
        .then(() => {
            Swal.close();
            Swal.fire({
                title: 'Enter OTP',
                input: 'text',
                inputLabel: 'OTP sent to your email',
                inputPlaceholder: 'Enter OTP',
                showCancelButton: false,
                confirmButtonText: 'Verify OTP',
                customClass: {
                    confirmButton: 'btn btn-primary'
                },
                buttonsStyling: false,
                preConfirm: (otp) => {
                    if (!otp) return Swal.showValidationMessage('Please enter the OTP');
                    return axios.post('/otp/verify', { email, otp })
                        .then(response => {
                            const span = document.querySelector(`#verification_${index}`);
                            span.classList.add('text-success');
                            span.setAttribute('data-verified', 'true');
                            return response.data.status === 'verified' || Swal.showValidationMessage('Invalid OTP');
                        })
                        .catch(() => Swal.showValidationMessage('Verification failed'));
                }
            }).then(result => {
                if (result.isConfirmed && result.value) {
                    Swal.fire({ icon: 'success', title: 'Email verified!', text: `Email: ${email}` });
                    btn.innerText = "Verified";
                    btn.disabled = true;
                    input.readOnly = true;
                } else {
                    btn.disabled = false;
                    btn.innerText = "Verify";
                }
            });
        })
        .catch(error => {
            console.error('Error sending OTP:', error);
            Swal.close();
            if (error.response && error.response.status === 422 && error.response.data && error.response.data.message) {
                Swal.fire('Failed', error.response.data.message, 'error');
            } else {
                Swal.fire('Failed', 'Unable to send OTP.', 'error');
            }
            btn.disabled = false;
            btn.innerText = "Verify";
        });
}

function isEmailVerified(index) {
    const span = document.querySelector(`#verification_${index}`);
    return span?.getAttribute('data-verified') === 'true';
}

function initIntlTelInput() {
    document.querySelectorAll(".phone-input").forEach((input) => {
        const iti = window.intlTelInput(input, {
            initialCountry: "IN",
            nationalMode: false,
            separateDialCode: true,
            formatOnDisplay: true,
            utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js"
        });

        input._iti = iti;
        input.addEventListener('input', () => {
            if (input.value.startsWith("0")) {
                input.value = input.value.replace(/^0+/, '');
            }
        });
    });
}

function initSubcategoryHandlers() {
    const subCategories = {
        'Government': ['Central Government', 'State Government', 'Diplomates', 'Others'],
        'Exhibitor': ['Exhibitors', 'Others'],
        'Media': ['Media', 'Others'],
        'Industry': ['Semiconductor Design', 'Semiconductor Manufacturing', 'Electronics Design', 'Electronics Manufacturing', 'Others'],
        'Academic': ['Students', 'Teachers', 'Professor', 'Others'],
        'Others': ['Others']
        // 'default': ['Select', 'Central Government', 'State Government', 'Diplomates', 'Exhibitors', 'Media', 'Semiconductor', 'Startups', 'Students', 'Teachers', 'Professor', 'Others']
    };

    for (let i = 0; i < attendeeCount; i++) {
        const catSelect = document.querySelector(`select[name="attendees[${i}][job_category]"]`);
        const subcatSelect = document.querySelector(`select[name="attendees[${i}][job_subcategory]"]`);
        const othersInput = document.getElementById(`others-category-input-${i}`);

        if (!catSelect || !subcatSelect || !othersInput) continue;

        const updateSubcategories = (category, selected = '') => {
            const options = subCategories[category] || subCategories['default'];
            subcatSelect.innerHTML = `<option value="">--- Select ---</option>`;
            options.forEach(opt => {
                subcatSelect.innerHTML += `<option value="${opt}" ${selected === opt ? 'selected' : ''}>${opt}</option>`;
            });
        };

        const toggleOtherInput = () => {
            const category = catSelect.value;
            const subcategory = subcatSelect.value;
            const showOther = category === 'Others' || subcategory === 'Others';
            othersInput.style.display = showOther ? '' : 'none';
            othersInput.querySelector('input').required = showOther;
        };

        catSelect.addEventListener('change', () => {
            updateSubcategories(catSelect.value);
            toggleOtherInput();
        });
        subcatSelect.addEventListener('change', toggleOtherInput);

        updateSubcategories(catSelect.value, subcatSelect.dataset.selected);
        toggleOtherInput();
    }
}

function validateAndSubmitForm(e) {
    let valid = true;

    document.querySelectorAll(".phone-input").forEach((input, idx) => {
        const iti = input._iti;
        const number = iti.getNumber().replace(/^0+/, '');
        if (number.length < 8 || number.length > 15) {
            alert(`Attendee ${idx + 1}: Invalid mobile number length.`);
            valid = false;
            return;
        }
        input.value = number;
    });

    for (let i = 0; i < attendeeCount; i++) {

        //if the category is 'Exhibitor' or 'Industry', then purpose is required
        var jobCategory = document.querySelector(`select[name="attendees[${i}][job_category]"]`);
        console.log(`Validating attendee ${i + 1} with job category:`, jobCategory ? jobCategory.value : 'N/A');
        if (jobCategory && (jobCategory.value === 'Exhibitor' || jobCategory.value === 'Industry')) {
            console.log(`Job category is ${jobCategory.value}, checking purpose of visit...`);
            var purposeInputs = document.querySelectorAll(`input[name="attendees[${i}][purpose][]"]`);
            //check if the value is empty

            if (!purposeInputs || purposeInputs.length === 0) {
                alert(`Attendee ${i + 1}: Please select at least one purpose of visit.`);
                // Focus the first purpose checkbox
                if (purposeInputs.length > 0) purposeInputs[0].focus();
                valid = false;
                //prevent from submitting the form
                e.preventDefault();
                break;
            }
            var businessNature = document.querySelector(`select[name="attendees[${i}][business_nature]"]`);
            console.log(`Validating attendee ${i + 1} with business nature:`, businessNature ? businessNature.value : 'N/A');
            if (businessNature?.required && !businessNature.value) {
                alert(`Attendee ${i + 1}: Please select the Nature of your Business.`);
                businessNature.focus();
                valid = false;
                //preveet from submitting the form
                e.preventDefault();
                break;
            }

            //validate the product category is not empty 
            var productCategory = document.querySelector(`select[name="attendees[${i}][product_category]"]`);
            console.log(`Validating attendee ${i + 1} with product category:`, productCategory ? productCategory.value : 'N/A');
            if (productCategory?.required && !productCategory.value) {
                alert(`Attendee ${i + 1}: Please select the Product Category.`);
                productCategory.focus();
                valid = false;
                //preveet from submitting the form
                e.preventDefault();
                break;
            }







        }



        if (!isEmailVerified(i)) {
            Swal.fire({
                icon: 'warning',
                title: 'Email Not Verified',
                text: `Attendee ${i + 1}: Please verify the email address before submitting.`
            });
            valid = false;
            break;
        }
    }

    if (!valid) e.preventDefault();
}



function initOtherCategoryWatcher(index) {
    var catSelect = document.querySelector(`select[name="attendees[${index}][job_category]"]`);
    var subcatSelect = document.querySelector(`select[name="attendees[${index}][job_subcategory]"]`);
    var otherField = document.getElementById(`others-category-input-${index}`);

    function toggleOtherField() {
        var category = catSelect.value;
        var subcategory = subcatSelect.value;
        var isOthers = category === 'Others' || subcategory === 'Others';
        if (otherField) {
            otherField.style.display = isOthers ? 'block' : 'none';
            var input = otherField.querySelector('input');
            if (input) input.required = isOthers;
        }
    }

    if (catSelect && subcatSelect) {
        catSelect.addEventListener('change', toggleOtherField);
        subcatSelect.addEventListener('change', toggleOtherField);
        toggleOtherField(); // Initial call
    }
}

function refillFormValues() {
    if (typeof oldAttendees === 'undefined' || !Array.isArray(oldAttendees)) return;
    for (let i = 0; i < attendeeCount; i++) {
        var oldData = oldAttendees[i] || {};
        Object.keys(oldData).forEach(key => {
            var input = document.querySelector(`[name="attendees[${i}][${key}]"]`);
            if (input && input.type !== 'checkbox' && input.type !== 'radio') {
                input.value = oldData[key];
            }
        });

        Object.keys(oldData).forEach(key => {
            if (Array.isArray(oldData[key])) {
                oldData[key].forEach(val => {
                    var checkbox = document.querySelector(`[name="attendees[${i}][${key}][]"][value="${val}"]`);
                    if (checkbox) checkbox.checked = true;
                });
            }
        });

        document.querySelectorAll(`select[name^="attendees[${i}]"]`).forEach(select => {
            var name = select.name.match(/\[(\w+)\]$/);
            if (name && oldData[name[1]]) {
                select.value = oldData[name[1]];
                select.dispatchEvent(new Event('change'));
            }
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    initIntlTelInput();
    initSubcategoryHandlers();
    refillFormValues();
    const form = document.getElementById("registrationForm");
    if (form) form.addEventListener("submit", validateAndSubmitForm);
});

document.addEventListener('DOMContentLoaded', () => {
    const totalAttendees = document.querySelectorAll('[data-index]').length;
    for (let i = 0; i < totalAttendees; i++) {
        initOtherCategoryWatcher(i);
    }
});



document.addEventListener('DOMContentLoaded', function () {
    // Dropdown toggle
    document.querySelectorAll('.event-days-dropdown').forEach(function (input) {
        input.addEventListener('click', function (e) {
            const idx = input.getAttribute('data-index');
            document.querySelectorAll('.dropdown-menu.event-days-menu[data-index="' + idx +
                '"]').forEach(function (menu) {
                    menu.classList.toggle('show');
                });
        });
    });

    // Sync checkboxes with select
    document.querySelectorAll('.event-day-checkbox').forEach(function (checkbox) {
        checkbox.addEventListener('change', function (e) {
            var idx = checkbox.getAttribute('data-index');
            var select = document.querySelector('.event-days-select[data-index="' + idx +
                '"]');
            var allCheckboxes = document.querySelectorAll(
                '.event-day-checkbox[data-index="' + idx + '"]');
            let selected = [];
            allCheckboxes.forEach(function (cb) {
                if (cb.checked) selected.push(cb.value);
            });
            // Remove all options selection
            Array.from(select.options).forEach(function (opt) {
                opt.selected = selected.includes(opt.value);
            });
            // Update text input display
            var input = document.querySelector('.event-days-dropdown[data-index="' + idx +
                '"]');
            input.value = selected.length ? selected.join(', ') : '';
        });
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function (e) {
        document.querySelectorAll('.dropdown-menu.event-days-menu').forEach(function (menu) {
            if (!menu.contains(e.target) && !menu.previousElementSibling.contains(e
                .target)) {
                menu.classList.remove('show');
            }
        });
    });

    // Initialize input value
    document.querySelectorAll('.event-days-dropdown').forEach(function (input) {
        var idx = input.getAttribute('data-index');
        var select = document.querySelector('.event-days-select[data-index="' + idx + '"]');
        let selected = [];
        Array.from(select.options).forEach(function (opt) {
            if (opt.selected) selected.push(opt.value);
        });
        input.value = selected.length ? selected.join(', ') : '';
    });
});

// write a function to when a user click on submit button, if some fields are not filled, show a sweet alert with the error message to select the field
function validateFormFields() {
    var requiredFields = document.querySelectorAll('[required]');
    let isValid = true;
    let errorMessage = '';

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            isValid = false;
            // Try to get a user-friendly label
            let label = '';
            var labelElem = document.querySelector(`label[for="${field.id}"]`);
            if (labelElem) {
                label = labelElem.innerText.trim();
            } else if (field.getAttribute('placeholder')) {
                label = field.getAttribute('placeholder');
            } else if (field.name) {
                label = field.name;
            } else {
                label = field.id || field.className || 'Unknown Field';
            }
            errorMessage += `Please fill the "${label}" field.\n`;
            console.log(`Empty field:`, { name: field.name, id: field.id, label });
        }
    });

    if (!isValid) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: errorMessage,
            confirmButtonText: 'OK'
        });
    }

    return isValid;
}

document.getElementById('registrationForm').addEventListener('submit', function (e) {
    if (!validateFormFields()) {
        e.preventDefault();
    }
});