{{-- @php
    dd(old());
@endphp --}}

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Event Visitor Registration</title>
  <link rel="icon" href="https://www.bengalurutechsummit.com/favicon-16x16.png" type="image/vnd.microsoft.icon" />
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&display=swap" rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/mdb-ui-kit/9.0.0/mdb.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.min.css" />
  {{-- <script src="https://www.google.com/recaptcha/api.js"></script> --}}

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
  .important {
    color: red;
  }
  .multiple-checkboxes{
    padding: 10px;
  }
  .multiselect-container.dropdown-menu {
  padding-left: 10px !important;
  padding-right: 10px !important;
  min-weight: 120px !important; 
}
.iti {
  width: 100%;
}

.iti--allow-dropdown .iti__flag-container {
  height: 100%;
}

.phone-input {
  padding-left: 48px !important; /* ensures text doesn't overlap with flag */
  height: 38px; /* match Bootstrap form-control height */
  line-height: 1.5;
}

input[type="tel"].phone-input.form-control {
  padding-left: 58px; /* depends on flag width, may require fine-tuning */
}
</style>
</head>
<body>
<div class="container py-5">
  <div class="container">
    <div class="row">
      
      
      <div class="col-md-8  test-start" >
        
      <img src="https://portal.semiconindia.org/assets/images/semi.jpg" class="img-fluid">
      </div>
      
      <div class="col-md-4 text-end">
      <svg width="163" height="40" viewBox="0 0 163 40" xmlns="http://www.w3.org/2000/svg"><path d="M43.751 18.973c-2.003-.363-4.369-.454-7.009-.363-8.011 9.623-20.846 17.974-29.403 19.064-2.093.272-3.641.091-4.915-.454.819.726 2.184 1.362 4.096 1.725 8.193 1.634 23.213-1.544 33.499-7.081 10.286-5.538 12.016-11.348 3.732-12.891zm-31.587 2.996c8.557-5.175 19.662-8.897 29.129-10.077C45.299 4.357 43.387-.454 35.923.545c-9.012 1.18-22.758 10.439-30.586 20.607-5.735 7.444-6.737 13.254-3.46 15.523-2.366-3.54 1.275-9.169 10.287-14.706zm58.35-.726l-4.643-1.271c-1.274-.363-1.911-.908-1.911-1.634 0-1.271 2.184-1.907 4.278-1.907 1.912 0 3.186.636 4.187 1.09.638.272 1.184.544 1.73.544 1.457 0 1.73-.635 1.73-1.18l-.182-.635c-.82-1.09-4.37-1.998-8.102-1.998-3.641 0-7.373 1.635-7.373 4.267 0 2.27 2.184 3.177 4.096 3.722l5.28 1.453c1.547.454 3.004.907 3.004 2.178 0 1.18-1.639 2.361-4.734 2.361-2.458 0-4.005-.817-5.098-1.453-.728-.363-1.274-.726-1.82-.726-.82 0-1.639.726-1.639 1.271 0 1.271 3.55 3.086 8.466 3.086 5.189 0 8.648-1.906 8.648-4.629-.091-2.724-3.004-3.722-5.917-4.539zm22.757-6.991c-6.554 0-10.013 4.086-10.013 8.08 0 3.722 2.731 8.079 10.559 8.079 5.371 0 9.103-2.178 9.103-3.268 0-1.271-1.183-1.271-1.638-1.271-.546 0-1.092.273-1.73.727-1.183.726-2.822 1.634-5.917 1.634-3.823 0-6.281-2.361-6.554-4.721h13.928c1.547 0 2.276-.454 2.276-1.452-.091-3.813-3.187-7.808-10.014-7.808zm6.19 6.991h-12.38c.273-2.452 2.367-4.812 6.19-4.812 3.732 0 5.917 2.451 6.19 4.812zm53.253-6.991c-1.093 0-1.73.545-1.73 1.544v12.981c0 .999.637 1.544 1.73 1.544 1.092 0 1.729-.545 1.729-1.544V15.796c0-.999-.637-1.544-1.729-1.544zm-26.399 2.633c1.457-1.543 4.096-2.633 6.645-2.633 4.006 0 8.375 1.816 8.375 5.72v8.896c0 .999-.637 1.543-1.73 1.543-1.092 0-1.729-.544-1.729-1.543v-8.442c0-2.542-1.639-3.722-4.916-3.722-2.458 0-5.006 1.361-5.006 3.722v8.442c0 .999-.638 1.543-1.73 1.543s-1.73-.544-1.73-1.543v-8.442c0-2.452-2.639-3.813-5.006-3.813-3.368 0-4.916 1.271-4.916 3.813v8.442c0 .999-.637 1.543-1.729 1.543-1.093 0-1.73-.544-1.73-1.543v-8.896c0-3.904 4.37-5.72 8.375-5.72 2.64 0 5.189.999 6.645 2.633l.182.091v-.091zm33.044-1.906h-.455a.196.196 0 0 1-.182-.182c0-.091.091-.181.182-.181h1.365c.091 0 .182.09.182.181a.196.196 0 0 1-.182.182h-.455v1.634c0 .091-.091.181-.182.181-.182 0-.182-.09-.182-.181v-1.634h-.091zm1.365 0c0-.273.091-.363.273-.363.091 0 .273 0 .364.181l.547 1.362.455-1.362c.091-.181.182-.181.364-.181s.273.09.273.363v1.634c0 .091-.091.181-.182.181s-.182-.09-.182-.181V15.07l-.546 1.543c0 .181-.091.181-.182.181s-.182-.09-.182-.181l-.547-1.543v1.543c0 .091-.091.181-.182.181s-.182-.09-.182-.181v-1.634h-.091z" id="Shape" fill-rule="nonzero"></path></svg>
      </div>
      
      
      </div>
    
    </div>    
  <h2>Event Visitor Registration</h2>
  <ul class="nav nav-pills mb-4 justify-content-center" id="stepIndicators">
    <li class="nav-item"><a class="nav-link active" id="step-indicator-1">1. Registration</a></li>
    {{-- <li class="nav-item"><a class="nav-link disabled" id="step-indicator-2">2. Review</a></li> --}}
    {{-- <li class="nav-item"><a class="nav-link disabled" id="step-indicator-3">3. Payment</a></li> --}}
  </ul>

  <form id="registrationForm" method="POST" action="{{ route('visitor_register') }}">
    @php
    //request from url
    $source = request()->query('source', 'default_source');
    $oldAttendees = old('attendees', []);

    @endphp
    @csrf
    <div id="attendeeContainer"></div>
    <button type="button" id="addAttendeeBtn" class="btn btn-secondary my-3 d-none">Add Attendee</button>

    <div class="row mb-3">
      <!-- Captcha Input -->
      <div class="col-md-6">
        <input type="text" name="captcha" id="captcha" class="form-control" maxlength="6" placeholder="Enter Captcha" required>
      </div>
    
      <!-- Captcha Image and Reload Button (Mobile: Stacked, Desktop: Side by Side) -->
      <div class="col-md-6 d-flex flex-column flex-md-row align-items-center mt-2">
        <div id="captcha-img" class="mb-2 mb-md-0 me-2">{!! captcha_img() !!}</div>
        <button type="button" class="btn btn-outline-secondary btn-sm" id="reload" title="Reload Captcha">
          ⟳
        </button>
      </div>
    </div>
    
    <script>
      document.getElementById('reload').addEventListener('click', function () {
        fetch('/reload-captcha')
          .then(response => response.json())
          .then(data => {
            document.getElementById('captcha-img').innerHTML = data.captcha;
          });
      });
    </script>
    
    
    
    
    
    </script>
    <button type="submit" class="btn btn-primary">Submit</button>
    {{-- <button class="g-recaptcha btn btn-primary" 
        data-sitekey="6LdNTRorAAAAALrJ7Z-fO3buyLsM7t6Hp18Akv6c" 
        data-callback='onSubmit' 
        data-action='submit'>Submit</button> --}}
  </form>

  <script>
    function onSubmit(token) {
      const form = document.getElementById("registrationForm");
      const requiredFields = form.querySelectorAll("[required]");
      let allFilled = true;
      let firstEmptyField = null;
  
      requiredFields.forEach(field => {
        const value = field.value?.trim();
  
        if (!value) {
          allFilled = false;
          firstEmptyField = field;
          return false;
        }
      });
  
      if (!allFilled) {
        const label = firstEmptyField.closest('.form-group, .col-md-4, .col-md-6, .col-md-12')?.querySelector('label')?.innerText || "A required field";
        Swal.fire({
          icon: 'error',
          title: 'Missing Information',
          text: `${label} is required. Please fill it out.`,
        });
      } else {
        form.submit();
      }
    }
  </script>
  
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>
<script>
        function verifyEmail(btn, index) {
          const input = btn.closest('.input-group').querySelector('input[type="email"]');
          const email = input.value.trim();
          if (!email) {
            Swal.fire({icon: 'warning', title: 'Please enter an email address first.'});
            return;
          }
          btn.disabled = true;
          btn.innerText = 'Verifying...';
          // Simulate async verification (replace with real AJAX if needed)
          setTimeout(() => {
            btn.disabled = false;
            btn.innerText = 'Verify';
            Swal.fire({icon: 'success', title: 'Email verified!', text: `Email: ${email}`});
          }, 1000);
        }
        </script>
<script>
          // Subcategories mapping
          const subCategories = {
            'Government': ['Central Government','State Government', 'Diplomates', 'Others'],
            'Exhibitor': ['Exhibitors', 'Others'],
            'Media': ['Media', 'Others'],
            'Industry': ['Semiconductor', 'Startups', 'Others'],
            'Academic': ['Students', 'Teachers', 'Professor', 'Others'],
            'Others': ['Others'],
            'default': ['Select','Central Government','State Government', 'Diplomates', 'Exhibitors','Media','Semiconductor', 'Startups','Students', 'Teachers', 'Professor','Others']
          };

          // Set subcategory options based on selected category and old value
          function setSubcategoryOptions(index, selectedCategory, selectedSubcategory) {
            const subcatSelect = document.querySelector(`select[name="attendees[${index}][job_subcategory]"]`);
            let options = subCategories[selectedCategory] || subCategories['default'];
            subcatSelect.innerHTML = `<option value="">Select Subcategory</option>`;
            options.forEach(opt => {
              subcatSelect.innerHTML += `<option value="${opt}" ${selectedSubcategory === opt ? 'selected' : ''}>${opt}</option>`;
            });
          }

          // Attach category change listeners after rendering
          setTimeout(() => {
            for (let i = 0; i < attendeeCount; i++) {
              const oldData = oldAttendees[i] || {};
              const catSelect = document.querySelector(`select[name="attendees[${i}][job_category]"]`);
              if (catSelect) {
                catSelect.addEventListener('change', function() {
                  setSubcategoryOptions(i, this.value, '');
                });
                // Set initial subcategory options if old value exists
                setSubcategoryOptions(i, oldData.job_category || '', oldData.job_subcategory || '');
              }
            }
          }, 1000);
        </script>


<script>
// display error using sweet alert
function showError(message) {
  Swal.fire({
    icon: 'error',
    title: 'Oops...',
    text: message,
  });
}

// show error from laravel validation
@if ($errors->any())
  @foreach ($errors->all() as $error)
    showError("{{ $error }}");
  @endforeach
@endif

const oldAttendees = @json($oldAttendees);
console.log(oldAttendees);
const maxAttendees = {{ $maxAttendees }};
const sectors = @json($natureOfBusiness);
//for each sectors display name 
for (let i = 0; i < sectors.length; i++) {
  sectors[i] = sectors[i].name;
// console.log(sectors[i]);
}

const productCategories = @json($productCategories);
const jobFunctions = @json($jobFunctions);
let attendeeCount = maxAttendees;
let countryList = [];

function renderSelectOptions(options) {
  return options.map(option => `<option value="${option}">${option}</option>`).join('');
}

function renderSelectOptions(options, selectedValues = []) {
  return options.map(opt => `
    <option value="${opt}" ${selectedValues.includes(opt) ? 'selected' : ''}>${opt}</option>
  `).join('');
}

function renderAttendeeForm(index, oldData = {}) {
  return `
  <div class="card mb-4">
    <div class="card-body">
      <h5>Attendee Information</h5>
      <div class="row g-3">
        <div class="col-md-4">
          <label>Title <span class="important">*</span></label>
          <select name="attendees[${index}][title]" class="form-control" required>
            <option value="" disabled ${!oldData.title ? 'selected' : ''}>--- Title ---</option>
            <option value="mr" ${oldData.title === 'mr' ? 'selected' : ''}>Mr.</option>
            <option value="mrs" ${oldData.title === 'mrs' ? 'selected' : ''}>Mrs.</option>
            <option value="ms" ${oldData.title === 'ms' ? 'selected' : ''}>Ms.</option>
            <option value="dr" ${oldData.title === 'dr' ? 'selected' : ''}>Dr.</option>
            <option value="prof" ${oldData.title === 'prof' ? 'selected' : ''}>Prof.</option>
          </select>
        </div>
        <div class="col-md-4">
          <label>First Name <span class="important">*</span></label>
          <input type="text" name="attendees[${index}][first_name]" class="form-control" required value="${oldData.first_name || ''}">
        </div>
        <div class="col-md-4">
          <label>Last Name <span class="important">*</span></label>
          <input type="text" name="attendees[${index}][last_name]" class="form-control" required value="${oldData.last_name || ''}">
        </div>
        <div class="col-md-4">
          <label>Designation <span class="important">*</span></label>
          <input type="text" name="attendees[${index}][designation]" class="form-control" required value="${oldData.designation || ''}">
        </div>
        <div class="col-md-4">
          <label>Company Name <span class="important">*</span></label>
          <input type="text" name="attendees[${index}][company]" class="form-control" required value="${oldData.company || ''}">
        </div>
        <div class="col-md-4">
          <label>Address</label>
          <input type="text" name="attendees[${index}][address]" class="form-control" value="${oldData.address || ''}">
        </div>
        <div class="col-md-4">
          <label>Country <span class="important">*</span></label>
          <select class="form-select country-dropdown" name="attendees[${index}][country]" data-index="${index}" required>
            <option value="${oldData.country || ''}" selected>${oldData.country || '--- Select ---'}</option>
          </select>
        </div>
        <div class="col-md-4">
          <label>State <span class="important">*</span></label>
          <select class="form-select state-dropdown" name="attendees[${index}][state]" data-index="${index}" required>
            <option value="${oldData.state || ''}" selected>${oldData.state || '--- Select ---'}</option>
          </select>
        </div>
        <div class="col-md-4">
          <label>City <span class="important">*</span></label>
          <select class="form-select city-dropdown" name="attendees[${index}][city]" data-index="${index}" required>
            <option value="${oldData.city || ''}" selected>${oldData.city || '--- Select ---'}</option>
          </select>
        </div>
        <div class="col-md-4">
          <label>Postal Code <span class="important">*</span></label>
          <input type="number" name="attendees[${index}][postal_code]" class="form-control" required value="${oldData.postal_code || ''}">
        </div>
        <div class="col-md-4 position-relative">
          <label>Mobile Number <span class="important">*</span></label>
          <input type="tel" name="attendees[${index}][mobile]" class="form-control phone-input" required value="${oldData.mobile || ''}">
        </div>
        <div class="col-md-4">
          <label>Email Address <span id="verification" class="text-warning" style="">(UNVERIFIED)</span> <span class="important">*</span> </label>
          <div class="input-group">
            <input type="email" name="attendees[${index}][email]" class="form-control" required value="${oldData.email || ''}">
            <button type="button" class="btn btn-outline-primary" onclick="verifyEmail(this, ${index})">Verify</button>
          </div>
        </div>
        

        <input type="hidden" name="attendees[${index}][source]" value="${oldData.source || 'default_source'}">

        <!-- Purpose Checkboxes -->
        <div class="col-md-12">
          <label class="form-label d-block">The purpose of your visit:<span class="text-danger">*</span></label>
          <div class="row">
            ${[
              "Purchase new products and services",
              "Source new vendors for an ongoing project",
              "Join the buyer-seller program & meet potential suppliers",
              "To connect & engage with existing suppliers",
              "Stay up to date with the latest innovations",
              "Compare and Benchmark technologies / solutions"
            ].map((label, i) => `
              <div class="col-md-6">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" id="purpose${i + 1}_${index}"
                         name="attendees[${index}][purpose][]" value="${label}"
                         ${oldData.purpose?.includes(label) ? 'checked' : ''}>
                  <label class="form-check-label" for="purpose${i + 1}_${index}">${label}</label>
                </div>
              </div>`).join('')}
          </div>
        </div>

        <!-- Product Categories Multi-select -->
        <div class="col-md-12 mt-3 d-flex flex-column flex-md-row align-items-start">
          <label for="products_${index}" class="me-md-3 mb-2 mb-md-0 pt-1" style="white-space: nowrap; min-width: 240px;">
            Product Categories of your interest: <span class="important">*</span>
          </label>
         <select id="multiple-checkboxes"
          multiple="multiple"
          name="attendees[${index}][products][]"
          required
          class="form-control"
          style="max-height: 200px; min-width: 250px;">
            ${renderSelectOptions(productCategories, oldData.products || [])}
          </select>
        </div>

        <!-- Business Nature -->
        <div class="col-md-12 mt-3">
          <label>Nature of your Business: <span class="important">*</span></label>
          <select class="form-select" name="attendees[${index}][business_nature]" required>
            ${renderSelectOptions(sectors, [oldData.business_nature])}
          </select>
        </div>

        <!-- Job Function -->
        <div class="col-md-12">
          <label class="fw-bold mb-2">Your primary job function:</label>
          <div class="row">
            <div class="col-md-6 mb-2">
              <label>Category <span class="important">*</span></label>
              <select class="form-select category-select" name="attendees[${index}][job_category]" data-index="${index}" required>
                <option value="">Select Category</option>
                <option value="Industry" ${oldData.job_category === 'Industry' ? 'selected' : ''}>Industry</option>
                <option value="Government" ${oldData.job_category === 'Government' ? 'selected' : ''}>Government Organization</option>
                <option value="Exhibitor" ${oldData.job_category === 'Exhibitor' ? 'selected' : ''}>Exhibitor</option>
                <option value="Academic" ${oldData.job_category === 'Academic' ? 'selected' : ''}>Academic</option>
                <option value="Media" ${oldData.job_category === 'Media' ? 'selected' : ''}>Media</option>
                <option value="Others" ${oldData.job_category === 'Others' ? 'selected' : ''}>Others</option>
              </select>
            </div>
            <div class="col-md-6 mb-2">
              <label>Subcategory <span class="important">*</span></label>
              <select class="form-select subcategory-select" name="attendees[${index}][job_subcategory]" required>
                <option value="">Select Subcategory</option>
              </select>
            </div>
          </div>
        </div>
        

        <!-- Consent -->
        <div class="col-md-12">
          <div class="form-check">
            <input type="checkbox" name="attendees[${index}][consent]" class="form-check-input" required ${oldData.consent === 'on' ? 'checked' : ''}>
            <label class="form-check-label">I acknowledge that I have read the consent terms and agree.</label>
          </div>
        </div>
      </div>
    </div>
  </div>`;
}
function renderForm() {
  const container = document.getElementById("attendeeContainer");
  container.innerHTML = "";
  for (let i = 0; i < attendeeCount; i++) {
    const oldData = oldAttendees[i] || {};
    container.innerHTML += renderAttendeeForm(i, oldData);
  }

  initIntlTelInput();
  for (let i = 0; i < attendeeCount; i++) {
    const oldData = oldAttendees[i] || {};
    loadCountries(i);

    // Add a short delay to ensure countries are loaded before setting
    setTimeout(() => {
      const countrySelect = document.querySelector(`select[name="attendees[${i}][country]"]`);
      if (countrySelect && oldData.country) {
        countrySelect.value = oldData.country;
        countrySelect.dispatchEvent(new Event("change"));
      }

      // Now delay states/cities after setting country
      setTimeout(() => {
        if (oldData.state) {
          const stateSelect = document.querySelector(`select[name="attendees[${i}][state]"]`);
          if (stateSelect) {
            stateSelect.value = oldData.state;
            stateSelect.dispatchEvent(new Event("change"));
          }
        }

        if (oldData.city) {
          const citySelect = document.querySelector(`select[name="attendees[${i}][city]"]`);
          if (citySelect) {
            citySelect.value = oldData.city;
          }
        }
      }, 400);

    }, 600);

    attachDropdownListeners(i);
  }

  if (maxAttendees > 1) document.getElementById("addAttendeeBtn").classList.remove("d-none");
}

function initIntlTelInput() {
  document.querySelectorAll(".phone-input").forEach((input, index) => {
    const iti = window.intlTelInput(input, {
      initialCountry: "auto",
      nationalMode: false,
      formatOnDisplay: true,
      separateDialCode: false,
      geoIpLookup: function (callback) {
        fetch("https://ipapi.co/json")
          .then(res => res.json())
          .then(data => callback(data.country_code));
      },
      utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js"
    });

    input._iti = iti;
    input.addEventListener('input', function () {
      let val = input.value;
      if (val.startsWith("0")) {
        input.value = val.replace(/^0+/, '');
      }
    });
  });
}



// Form Submission Validation

const form = document.getElementById("registrationForm");
form.addEventListener("submit", function (e) {
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
    const checked = document.querySelectorAll(`input[name="attendees[${i}][purpose][]"]:checked`);
    if (checked.length === 0) {
      alert(`Attendee ${i + 1}: Please select at least one purpose of visit.`);
      valid = false;
      break;
    }
  }

  if (!valid) e.preventDefault();
});

const apiKey = 'WTYxaXZYcmVlbU1Mdzd2MVZxc00yd1BHUEZGUGFLR1NYRTYxQmthOA==';
const headers = { "X-CSCAPI-KEY": apiKey };

function loadCountries(index) {
  axios.get('https://api.countrystatecity.in/v1/countries', { headers })
    .then(res => {
      countryList = res.data;
      const select = document.querySelector(`select[name="attendees[${index}][country]"]`);
      select.innerHTML = `<option value="">Select Country</option>`;
      countryList.forEach(c => {
        select.innerHTML += `<option value="${c.name}">${c.name}</option>`;
      });
    });
}

function loadStates(selectedCountryName, index) {
  const stateSelect = document.querySelector(`select[name="attendees[${index}][state]"]`);
  const citySelect = document.querySelector(`select[name="attendees[${index}][city]"]`);
  const selectedCountry = countryList.find(c => c.name === selectedCountryName);
  if (!selectedCountry) {
    stateSelect.innerHTML = `<option value="${selectedCountryName}" selected>${selectedCountryName}</option>`;
    citySelect.innerHTML = `<option value="${selectedCountryName}" selected>${selectedCountryName}</option>`;
    return;
  }
  axios.get(`https://api.countrystatecity.in/v1/countries/${selectedCountry.iso2}/states`, { headers })
    .then(res => {
      const states = res.data;
      if (states.length === 0) {
        stateSelect.innerHTML = `<option value="${selectedCountryName}" selected>${selectedCountryName}</option>`;
        citySelect.innerHTML = `<option value="${selectedCountryName}" selected>${selectedCountryName}</option>`;
      } else {
        stateSelect.innerHTML = `<option value="">Select State</option>`;
        states.forEach(s => {
          stateSelect.innerHTML += `<option value="${s.name}">${s.name}</option>`;
        });
      }
    });
}

function loadCities(countryName, stateName, index) {
  const citySelect = document.querySelector(`select[name="attendees[${index}][city]"]`);
  const selectedCountry = countryList.find(c => c.name === countryName);
  if (!selectedCountry) {
    citySelect.innerHTML = `<option value="${stateName}" selected>${stateName}</option>`;
    return;
  }
  axios.get(`https://api.countrystatecity.in/v1/countries/${selectedCountry.iso2}/states`, { headers })
    .then(res => {
      const matchingState = res.data.find(s => s.name === stateName);
      if (!matchingState) {
        citySelect.innerHTML = `<option value="${stateName}" selected>${stateName}</option>`;
        return;
      }
      axios.get(`https://api.countrystatecity.in/v1/countries/${selectedCountry.iso2}/states/${matchingState.iso2}/cities`, { headers })
        .then(res => {
          const cities = res.data;
          if (cities.length === 0) {
            citySelect.innerHTML = `<option value="${stateName}" selected>${stateName}</option>`;
          } else {
            citySelect.innerHTML = `<option value="">Select City</option>`;
            cities.forEach(c => {
              citySelect.innerHTML += `<option value="${c.name}">${c.name}</option>`;
            });
          }
        });
    });
}

function attachDropdownListeners(index) {
  const countrySelect = document.querySelector(`select[name="attendees[${index}][country]"]`);
  const stateSelect = document.querySelector(`select[name="attendees[${index}][state]"]`);
  countrySelect.addEventListener("change", function () {
    loadStates(this.value, index);
  });
  stateSelect.addEventListener("change", function () {
    loadCities(countrySelect.value, this.value, index);
  });
}

renderForm();


        
</script>

<script src="/assets/js/jquery.min.js"></script>
  <script src="/assets/js/popper.js"></script>
  <script src="/assets/js/bootstrap.min.js"></script>
  <script src="/assets/js/bootstrap-multiselect.js"></script>
  <script src="/assets/js/main.js"></script>
</body>
</html>