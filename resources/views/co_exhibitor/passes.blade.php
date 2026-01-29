@extends('layouts.users')
@section('title', 'Co Exhibitor Dashboard')
@section('content')
@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/css/intlTelInput.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
@endpush

<style>
    .iti {
        width: 100%;
    }

    .iti__flag {
        background-image: url("https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/img/flags.png");
    }

    @media (-webkit-min-device-pixel-ratio: 2),
    (min-resolution: 192dpi) {
        .iti__flag {
            background-image: url("https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/img/flags@2x.png");
        }
    }
</style>

<div class="container-fluid py-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-6">
            <h3 class="mb-0 h4 font-weight-bolder">Co Exhibitor Dashboard</h3>
        </div>
        <div class="col-md-6 text-md-end">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb justify-content-md-end mb-0">
                    <li class="breadcrumb-item"><a href="#" class="text-decoration-none">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Exhibition Passes</h6>
                    <div>
                        <button type="button" class="btn btn-primary btn-sm me-2" data-bs-toggle="modal" data-bs-target="#addExhibitorModal">
                            <i class="fas fa-user-plus me-2"></i>Add Exhibitor
                        </button>
                        {{-- <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#inviteModal">
                            <i class="fas fa-plus me-2"></i>Invite
                        </button> --}}
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="text-sm mb-0">Total Passes</h6>
                                    <h3 class="font-weight-bold mb-0">{{ $counts['total'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-success bg-opacity-10">
                                <div class="card-body">
                                    <h6 class="text-sm mb-0">Used Passes</h6>
                                    <h3 class="font-weight-bold mb-0">{{ $counts['used'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-info bg-opacity-10">
                                <div class="card-body">
                                    <h6 class="text-sm mb-0">Remaining Passes</h6>
                                    <h3 class="font-weight-bold mb-0">{{ $counts['remaining'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Name</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Email</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Phone Number</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Job Title</th>
                                    <th class="text-uppercase text-secondary text-xs font-weight-bolder opacity-7">Organisation Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($exhibitors as $exhibitor)
                                <tr>
                                    <td>
                                        {{ $exhibitor['first_name'] }}{{ $exhibitor['last_name'] ? ' ' . $exhibitor['last_name'] : '' }}
                                    </td>
                                    <td>
                                        {{ $exhibitor['email'] }}
                                    </td>
                                    <td>
                                        {{ $exhibitor['mobile'] }}
                                    </td>
                                    <td>
                                        {{ $exhibitor['job_title'] }}
                                    </td>
                                    <td>
                                        {{ $exhibitor['organisation_name'] ?? '-' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


</div>

<!-- Invite Modal -->
<div class="modal fade" id="inviteModal" tabindex="-1" aria-labelledby="inviteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="inviteModalLabel">Invite Exhibitor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="invite_email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="invite_email" name="email" required placeholder="Enter email address">
                    </div>
                    @if(session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                    @endif
                    @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send Invitation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Exhibitor Modal -->
<div class="modal fade" id="addExhibitorModal" tabindex="-1" aria-labelledby="addExhibitorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addExhibitorModalLabel">
                    <i class="fas fa-user-plus me-2"></i>Add Exhibitor
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST" id="addExhibitorForm" class="needs-validation" novalidate>
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control custom-input" id="first_name" name="first_name" required placeholder="First Name">
                                <label for="first_name">First Name</label>
                                <div class="invalid-feedback">Please enter first name</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control custom-input" id="last_name" name="last_name" placeholder="Last Name">
                                <label for="last_name">Last Name</label>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="email" class="form-control custom-input" id="exhibitor_email" name="email" required placeholder="Email Address">
                                <label for="exhibitor_email">Email Address</label>
                                <div class="invalid-feedback">Please enter a valid email address</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group phone-input-container">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" class="form-control custom-input" id="phone" name="phone" required style="width: 100%">
                                <div class="invalid-feedback">Please enter a valid phone number</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" class="form-control custom-input" id="job_title" name="job_title" required placeholder="Job Title">
                                <label for="job_title">Job Title</label>
                                <div class="invalid-feedback">Please enter job title</div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-floating">
                                <input type="text" class="form-control custom-input" id="organisation_name" name="organisation_name" value="{{ $coExhibitor->co_exhibitor_name }}" required placeholder="Organisation Name">
                                <label for="organisation_name">Organisation Name</label>
                                <div class="invalid-feedback">Please enter organisation name</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-floating">
                                <select class="form-select custom-input" id="idCardType" name="idCardType" required>
                                    <option value="" disabled selected>Select ID Card Type</option>
                                    <option value="Passport">Passport</option>
                                    <option value="Aadhar Card">Aadhar Card</option>
                                    <option value="PAN Card">PAN Card</option>
                                    <option value="Voter ID">Voter ID</option>
                                    <option value="Driving License">Driving License</option>
                                </select>
                                <label for="idCardType">ID Card Type</label>
                                <div class="invalid-feedback">Please select ID card type</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" class="form-control custom-input" id="idCardNumber" name="idCardNumber" required placeholder="ID Card Number">
                                <label for="idCardNumber">ID Card Number</label>
                                <div class="invalid-feedback">Please enter ID card number</div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-2"></i>Add Exhibitor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .custom-input {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .custom-input:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }

    .form-floating>.custom-input {
        padding-top: 1.625rem;
        padding-bottom: 0.625rem;
    }

    .phone-input-container .iti {
        width: 100%;
    }

    .phone-input-container input {
        padding-left: 90px !important;
    }

    .modal-content {
        border: none;
        border-radius: 12px;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    }

    .modal-header {
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        padding: 1rem 1.5rem;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        padding: 1rem 1.5rem;
        border-bottom-left-radius: 12px;
        border-bottom-right-radius: 12px;
    }

    .btn {
        padding: 0.5rem 1.5rem;
        font-weight: 500;
        border-radius: 6px;
        transition: all 0.3s ease;
    }

    .btn:hover {
        transform: translateY(-1px);
    }

    .form-floating>label {
        padding: 1rem 0.75rem;
    }

    .invalid-feedback {
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }
</style>


<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/intlTelInput.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const phoneInput = document.querySelector("#phone");
        const iti = window.intlTelInput(phoneInput, {
            initialCountry: "in",
            preferredCountries: ["in", "us", "gb"],
            separateDialCode: true,
            formatOnDisplay: true,
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.19/js/utils.js",
            autoPlaceholder: "polite",
            customPlaceholder: null,
            nationalMode: false,
            autoHideDialCode: false,
            allowDropdown: true
        });

        // Form validation
        const form = document.getElementById('addExhibitorForm');
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            if (!form.checkValidity()) {
                e.stopPropagation();
                form.classList.add('was-validated');
                return;
            }

            // Validate phone number
            if (!iti.isValidNumber()) {
                phoneInput.classList.add('is-invalid');
                return;
            } // Store the full phone number with country code
            const fullNumber = iti.getNumber();
            phoneInput.value = fullNumber; // Log the email value before creating formData
            const emailValue = document.getElementById('exhibitor_email').value;
            console.log('Email value:', emailValue);

            // Prepare form data
            const formData = {
                _token: '{{ csrf_token() }}',
                name: document.getElementById('first_name').value + (document.getElementById('last_name').value ? ' ' + document.getElementById('last_name').value : ''),
                email: document.getElementById('exhibitor_email').value,
                phone: fullNumber,
                jobTitle: document.getElementById('job_title').value,
                organisationName: document.getElementById('organisation_name').value,
                invite_type: 'exhibitor',
                idCardType: document.getElementById('idCardType').value,
                idCardNumber: document.getElementById('idCardNumber').value
            };

            console.log("Submitting Form Data: ", formData);

            // Send AJAX request
            fetch('/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': formData._token
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        Swal.fire({
                            title: 'Error',
                            text: typeof data.error === 'string' ? data.error : JSON.stringify(data.error),
                            icon: 'error'
                        });
                    } else {
                        Swal.fire({
                            title: 'Success',
                            text: data.message || 'Exhibitor added successfully!',
                            icon: 'success'
                        }).then(() => {
                            const modal = bootstrap.Modal.getInstance(document.getElementById('addExhibitorModal'));
                            modal.hide();
                            location.reload();
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Something went wrong! ' + error.message,
                        icon: 'error'
                    });
                });
        });

        // Real-time phone validation
        phoneInput.addEventListener('blur', function() {
            if (iti.isValidNumber()) {
                phoneInput.classList.remove('is-invalid');
                phoneInput.classList.add('is-valid');
            } else {
                phoneInput.classList.remove('is-valid');
                phoneInput.classList.add('is-invalid');
            }
        });

        // Add animation to form inputs
        const inputs = document.querySelectorAll('.custom-input');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            input.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });
    });
</script>

@endsection