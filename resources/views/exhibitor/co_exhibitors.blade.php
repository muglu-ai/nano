@extends('layouts.users')
@section('title', 'Co-Exhibitors List')
@section('content')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/css/intlTelInput.min.css" />


    <style>
        #coExhibitorForm input[type="text"],
        #coExhibitorForm input[type="email"],
        #coExhibitorForm input[type="file"],
        #coExhibitorForm textarea {
            border: 1px solid #ced4da;
            border-radius: 8px;
            padding: 10px 12px;
            background-color: #fff;
            transition: border-color 0.2s, box-shadow 0.2s;
            font-size: 15px;
            box-shadow: none;
        }

        /* On focus */
        #coExhibitorForm input:focus,
        #coExhibitorForm textarea:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
            outline: none;
        }

        /* File input style reset */
        #coExhibitorForm input[type="file"] {
            padding: 8px;
        }

        /* Label styling for bold and spacing */
        #coExhibitorForm .form-label {
            font-weight: 600;
            margin-bottom: 6px;
        }

        /* Force consistent height and padding with Bootstrap form fields */
        .iti {
            width: 100%;
        }

        .iti__flag-container {
            padding: 0;
        }

        .iti input[type="tel"] {
            width: 100%;
            height: 100%;
            border-radius: 8px;
            border: 1px solid #ced4da;
            font-size: 15px;
            transition: border-color 0.2s, box-shadow 0.2s;
            background-color: #fff;
        }

        .iti input[type="tel"]:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
            outline: none;
        }

        /* Make the dropdown flag button match */
        .iti__flag-container .iti__selected-flag {
            height: 100%;
            border-radius: 8px 0 0 8px;
            background-color: #f8f9fa;
            border-right: 1px solid #ced4da;
        }
    </style>
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col">
                <h3 class="mb-0 h4 font-weight-bolder">Dashboard</h3>
            </div>
            <div class="col d-flex justify-content-end align-items-center gap-3">
                <a href="#" data-bs-toggle="modal" data-bs-target="#termsConditionsModal"
                    class="btn btn-outline-secondary btn-sm">
                    View Terms & Conditions
                </a>

                <button class="btn btn-primary" onclick="showCoExhibitorForm()">Add Co-Exhibitor</button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-flush mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th class="text-left  text-uppercase text-white text-md font-weight-bolder  ps-2">Name</th>
                            <th class="text-left  text-uppercase text-white text-md font-weight-bolder  ps-2">Contact Person
                            </th>
                            <th class="text-left  text-uppercase text-white text-md font-weight-bolder  ps-2">Email</th>
                            <th class="text-left  text-uppercase text-white text-md font-weight-bolder  ps-2">Phone</th>
                            <th class="text-left  text-uppercase text-white text-md font-weight-bolder  ps-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($coExhibitors->isEmpty())
                            <tr>
                                <td colspan="5" class="text-center">No co-exhibitor is added</td>
                            </tr>
                        @else
                            @foreach ($coExhibitors as $coExhibitor)
                                <tr>
                                    <td class="text-left text-dark text-md">{{ $coExhibitor->co_exhibitor_name }}</td>
                                    <td class="text-left text-dark text-md">{{ $coExhibitor->contact_person }}</td>
                                    <td class="text-left text-dark text-md">{{ $coExhibitor->email }}</td>
                                    <td class="text-left text-dark text-md">{{ $coExhibitor->phone }}</td>
                                    <td class="text-left text-dark text-md"><span
                                            class=" badge d-block w-45 bg-{{ $coExhibitor->status == 'approved' ? 'success' : 'danger' }}">{{ ucfirst($coExhibitor->status) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div id="coExhibitorModal" class="modal fade" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content shadow-lg border-0 rounded-3">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">Co-Exhibitor Application Form</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>

                    <div class="modal-body px-4 py-3">
                        <form id="coExhibitorForm" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="application_id" value="{{ $application->id }}">

                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label for="co_exhibitor_name" class="form-label fw-bold">Company Name of the
                                        Co-Exhibitor </label>
                                    <input type="text" class="form-control" id="co_exhibitor_name"
                                        name="co_exhibitor_name" placeholder="Enter company name" required>
                                </div>

                                <div class="col-md-12">
                                    <label for="address" class="form-label fw-bold">Address</label>
                                    <textarea class="form-control" id="address" name="address" rows="2" placeholder="Enter address" required></textarea>
                                </div>

                                <div class="col-md-6">
                                    <label for="contact_person" class="form-label fw-bold">Contact Person</label>
                                    <input type="text" class="form-control" id="contact_person" name="contact_person"
                                        placeholder="Enter contact name" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="job_title" class="form-label fw-bold">Job Title</label>
                                    <input type="text" class="form-control" id="job_title" name="job_title"
                                        placeholder="Enter job title" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label fw-bold">Email</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                        placeholder="example@domain.com" required>
                                </div>

                                <div class="col-md-6">
                                    <label for="phone" class="form-label fw-bold">TEL</label>
                                    <input type="tel" class="form-control" id="phone" name="phone"
                                        placeholder="Enter phone number" required>
                                </div>

                                <div class="col-md-12">
                                    <label for="proof_document" class="form-label fw-bold">Proof Document Upload</label>
                                    <input type="file" class="form-control" id="proof_document" name="proof_document"
                                        accept=".pdf,.doc,.docx,.jpg,.png" required>
                                    <small class="text-muted">Accepted formats: PDF, DOC, JPG, PNG</small>
                                </div>
                            </div>

                            <div class="text-end mt-4">
                                <button type="button" class="btn btn-primary px-4"
                                    onclick="submitCoExhibitor()">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>



        <!-- Terms & Conditions Modal -->
        <div class="modal fade" id="termsConditionsModal" tabindex="-1" aria-labelledby="termsLabel"
            aria-hidden="true" data-bs-backdrop="{{ $coex_terms_accepted ? 'true' : 'static' }}"
            data-bs-keyboard="{{ $coex_terms_accepted ? 'true' : 'false' }}">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="termsLabel">Co-Exhibitor Terms & Conditions</h5>
                    </div>
                    <div class="modal-body">
                        <p>Co-exhibitors are companies that participate within a primary exhibitor’s booth. To be officially
                            recognized as a co-exhibitor at <strong>SEMICON® India</strong>, the following regulations must
                            be followed:</p>

                        <hr>
                        <h6>Eligibility Criteria</h6>
                        <p>The primary exhibitor must have one of the following relationships with the co-exhibitor:</p>
                        <ul>
                            <li>A subsidiary company</li>
                            <li>A group company</li>
                            <li>An agent or a distributor</li>
                        </ul>

                        <hr>
                        <h6>How to Apply</h6>
                        <p>The primary exhibitor must submit a <strong>Co-Exhibitor Application Form</strong> along with
                            supporting documents proving the relationship, such as:</p>
                        <ul>
                            <li>Financial statements (for parent, subsidiary, or group companies)</li>
                            <li>A contract with the agent or distributor</li>
                        </ul>
                        <p><em>All documents are subject to review and recognition by SEMI.</em></p>

                        <hr>
                        <h6>Application Deadline</h6>
                        <p>Applications must be submitted by <strong>July 31st, 2025</strong>.</p>

                        <hr>
                        <h6>Application Fee</h6>
                        <p>Fees depend on the primary exhibitor's membership status:</p>
                        <ul>
                            <li><strong>Member:</strong> ₹25,000 per co-exhibitor (excluding tax)</li>
                            <li><strong>Non-member:</strong> ₹32,500 per co-exhibitor (excluding tax)</li>
                        </ul>

                        <hr>
                        <h6>Payment Terms</h6>
                        <ul>
                            <li>After SEMI’s approval, an invoice will be issued for 100% payment.</li>
                            <li>Payment must be completed by <strong>August 10th, 2025</strong>.</li>
                            <li>Privileges will be provided only after payment is received.</li>
                        </ul>

                        <hr>
                        <h6>Co-Exhibitor Privileges</h6>
                        <ul>
                            <li>Company name listed on the online exhibitor directory and floor plan</li>
                            <li>Access to a2z exhibitor portal to update:
                                <ul>
                                    <li>Company profile</li>
                                    <li>Product images and information</li>
                                    <li>Press releases (published on the SEMICON India website)</li>
                                </ul>
                            </li>
                        </ul>

                        <p class="mt-3"><strong>Note:</strong> All extra services (badge applications, booth upgrades,
                            etc.) can only be requested and managed by the <strong>primary exhibitor</strong>.</p>

                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="acceptCheckbox"
                                @if ($coex_terms_accepted) checked @endif
                                @if ($coex_terms_accepted) disabled @endif>
                            <label class="form-check-label" for="acceptCheckbox">
                                I have read and understood the co-exhibitor rules and payment terms.
                            </label>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="acceptBtn" class="btn btn-primary" disabled>Accept</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            function showCoExhibitorForm() {
                $('#coExhibitorModal').modal('show');
            }

            function submitCoExhibitor() {
                // Get the full international phone number
                const phoneInput = document.querySelector("#phone");
                const iti = window.intlTelInputGlobals.getInstance(phoneInput);
                const fullPhone = iti.getNumber(); // e.g., +919876543210

                // Create a FormData object
                const form = document.getElementById("coExhibitorForm");
                const formData = new FormData(form);

                // Add full phone number manually (if needed separately)
                formData.append('full_phone', fullPhone);

                $.ajax({
                    url: "{{ route('co_exhibitor.store') }}",
                    type: "POST",
                    data: formData,
                    processData: false, // Required for file upload
                    contentType: false, // Required for file upload
                    success: function(response) {
                        console.log(response);
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Co-Exhibitor request submitted for approval!',
                        }).then(() => {
                            location.reload();
                        });
                    },
                    error: function(xhr) {
                        let errorMessage = 'Something went wrong. Please try again!';
                        if (xhr.status === 400) {
                            errorMessage = xhr.responseJSON.error || 'Bad Request. Please check your input.';
                        } else if (xhr.status === 401) {
                            errorMessage = 'Unauthorized. Please log in.';
                        } else if (xhr.status === 403) {
                            errorMessage = 'Forbidden. You do not have permission.';
                        } else if (xhr.status === 404) {
                            errorMessage = 'Not Found. The requested resource could not be found.';
                        } else if (xhr.status === 422 && xhr.responseJSON.errors) {
                            const firstError = Object.values(xhr.responseJSON.errors)[0];
                            errorMessage = Array.isArray(firstError) ? firstError[0] : firstError;
                        } else if (xhr.status === 500) {
                            errorMessage = xhr.responseJSON.error ||
                                'Internal Server Error. Please try again later.';
                        }

                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMessage,
                        });
                    }
                });
            }
        </script>


        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const termsModal = new bootstrap.Modal(document.getElementById('termsConditionsModal'));
                const acceptBtn = document.getElementById('acceptBtn');
                const checkbox = document.getElementById('acceptCheckbox');

                // Prevent multiple popup by using localStorage
                if (!localStorage.getItem('accepted_coex_terms')) {
                    termsModal.show();
                }

                checkbox.addEventListener('change', () => {
                    acceptBtn.disabled = !checkbox.checked;
                });

                acceptBtn.addEventListener('click', () => {
                    fetch("{{ route('coex.acceptTerms') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({
                                accept_terms: true
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                localStorage.setItem('accepted_coex_terms', 'true');
                                termsModal.hide();
                            } else {
                                alert('Something went wrong.');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert('Error while saving acceptance.');
                        });
                });


            });

            //check if terms are accepted passed from the controller
            document.addEventListener("DOMContentLoaded", function() {
                const checkbox = document.getElementById('acceptCheckbox');
                const acceptBtn = document.getElementById('acceptBtn');

                if (checkbox.checked) {
                    acceptBtn.disabled = true;
                    //set the localStorage item to prevent multiple popups
                    localStorage.setItem('accepted_coex_terms', 'true');
                } else {
                    acceptBtn.disabled = false;
                    // remove the localStorage item if terms are not accepted
                    localStorage.removeItem('accepted_coex_terms');
                }
            });
        </script>


        <script>
            document.addEventListener("DOMContentLoaded", function() {
                const phoneInput = document.querySelector("#phone");

                window.intlTelInput(phoneInput, {
                    initialCountry: "in",
                    separateDialCode: true,
                    nationalMode: true,
                    preferredCountries: ["in", "us", "gb", "ae"],
                    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/utils.js"
                });
            });
        </script>

        <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.1.1/build/js/intlTelInput.min.js"></script>


    @endsection
