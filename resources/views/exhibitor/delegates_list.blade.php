@extends('layouts.users')
@section('title', $slug)
@section('content')
    <!-- intlTelInput Plugin -->
    @php
        if ($slug == 'Inaugural Passes') {
            $link = 'complimentary';
        } elseif ($slug == 'Stall Manning') {
            $link = 'stall_manning';
        }
    @endphp

    <style>
        .custom-form {
            max-width: 500px;
            margin: 0 auto;
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-control {
            height: 50px;
            padding: 0.75rem 1rem;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
            width: 100%;
        }

        .form-control:focus {
            border-color: #5e72e4;
            box-shadow: 0 0 0 0.2rem rgba(94, 114, 228, 0.15);
            background-color: #fff;
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #344767;
            margin-bottom: 0.5rem;
            display: block;
        }

        .form-submit-btn {
            width: 100%;
            padding: 0.75rem;
            border-radius: 8px;
            background: linear-gradient(310deg, #2152ff 0%, #21d4fd 100%);
            color: #fff;
            border: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .form-submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 7px 14px rgba(50, 50, 93, 0.1), 0 3px 6px rgba(0, 0, 0, 0.08);
        }

        .iti {
            width: 100%;
        }

        .iti__flag-container {
            height: 50px;
        }
        
        .invite-link {
            background: linear-gradient(45deg, #ff6b6b, #ff8e53);
            color: white !important;
            padding: 6px 12px;
            border-radius: 20px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
        }
        
        .invite-link:hover {
            background: linear-gradient(45deg, #ff5252, #ff7043);
            color: white !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.4);
            text-decoration: none;
        }
        
        .invite-note {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }
        
        .invite-note .note-icon {
            font-size: 1.2em;
            margin-right: 8px;
        }
        
        .guide-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .guide-header {
            background: rgba(255, 255, 255, 0.15);
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .guide-icon {
            font-size: 1.5em;
        }
        
        .guide-content {
            padding: 20px;
        }
        
        .guide-content ol {
            margin: 0;
            padding-left: 20px;
        }
        
        .guide-content li {
            margin-bottom: 12px;
            line-height: 1.6;
        }
        
        .guide-highlight {
            background: rgba(255, 255, 255, 0.25);
            padding: 4px 8px;
            border-radius: 8px;
            font-weight: 600;
            border: 1px solid rgba(255, 255, 255, 0.4);
            color: #2d3748;
        }
        
        .guide-tip {
            background: rgba(255, 255, 255, 0.2);
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
            border-left: 4px solid rgba(255, 255, 255, 0.6);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .tip-icon {
            font-size: 1.2em;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tableBody = document.querySelector('#datatable-basic tbody');
            const perPageSelector = document.querySelector('#perPage');
            const paginationContainer = document.querySelector('.pagination-container');
            let sortField = 'first_name'; // Default sorting by first_name
            let sortDirection = 'asc';
            let perPage = 10;
            async function fetchUsers(page = 1) {
                console.log(`Fetching users for page: ${page}`);
                try {
                    const response = await fetch(
                        `/exhibitor/list/${link}?page=${page}&sort=${sortField}&direction=${sortDirection}&per_page=${perPage}`, {
                            headers: {
                                'Accept': 'application/json'
                            }
                        });
                    if (!response.ok) throw new Error('Failed to fetch users');
                    const data = await response.json();
                    renderTable(data.data);
                    renderPagination(data);
                } catch (error) {
                    console.error('Error fetching users:', error);
                }
            }
            // <td class="text-sm font-weight-normal">
            //     <button class="btn btn-sm btn-primary" onclick="showConfirmationModal('${user.email}', '${user.first_name}')">Status</button>
            // </td>
            function renderTable(users) {
                tableBody.innerHTML = '';
                users.forEach(user => {
                    let nameCell;
                    
                    // Check if name is empty or null
                    if (!user.first_name && !user.last_name) {
                        // If name is empty/null and user has token, create hyperlink
                        if (user.token) {
                            nameCell = `<a href="/invited/inaugural/${user.token}/" target="_blank" class="invite-link">📧 Invite Link</a>`;
                        } else {
                            nameCell = 'N/A';
                        }
                    } else {
                        // If name exists, use the existing logic
                        nameCell = user.unique_id ?
                            `<a href="/exhibitor-pdf/${user.unique_id}" target="_blank">${user.first_name} ${user.last_name || ''}</a>` :
                            `${user.first_name} ${user.last_name || ''}`;
                    }
                    
                    const emailCell = user.token ? 
                        `<a href="/invited/inaugural/${user.token}/" target="_blank" class="invite-link">${user.email || 'N/A'}</a>` : 
                        `${user.email || 'N/A'}`;
                    
                    const row = `
            <tr>
                <td class="text-sm font-weight-normal">${nameCell}</td>
                <td class="text-sm font-weight-normal">${emailCell}</td>
                <td class="text-sm font-weight-normal">${user.job_title || 'N/A'}</td>
                <td class="text-sm font-weight-normal">${user.mobile || 'N/A'}</td>
                <td class="text-sm font-weight-normal">${user.organisation_name || 'N/A'}</td>
            </tr>`;
                    tableBody.innerHTML += row;
                });
            }

            function renderPagination(data) {
                paginationContainer.innerHTML = '';
                for (let i = 1; i <= data.last_page; i++) {
                    paginationContainer.innerHTML += `
            <button class="btn btn-sm ${data.current_page === i ? 'btn-primary' : 'btn-secondary'}"
                    data-page="${i}">
                ${i}
            </button>`;
                }

                // Add click listeners to pagination buttons
                document.querySelectorAll('.pagination-container button').forEach(button => {
                    button.addEventListener('click', function() {
                        fetchUsers(this.dataset.page);
                    });
                });
            }

            // Sorting headers
            document.querySelectorAll('.thead-light th').forEach(header => {
                header.addEventListener('click', function() {
                    const field = this.dataset.sort;
                    if (field) {
                        sortField = field;
                        sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                        fetchUsers();
                    }
                });
            });

            // Per page selector
            perPageSelector.addEventListener('change', function() {
                perPage = this.value;
                fetchUsers();
            });

            // Initial fetch
            fetchUsers();
        });
    </script>
    @php
        $extractedData = $data->map(function ($item) {
            return [
                'first_name' => $item->first_name,
                'last_name' => $item->last_name,
                'email' => $item->email,
                'job_title' => $item->job_title,
                'mobile' => $item->mobile,
                'organisation_name' => $item->organisation_name,
                'token' => $item->token,
                'unique_id' => $item->unique_id,
            ];
        });
        // dd($extractedData);
    @endphp
    @php
        $allocationName = 'Allocated';
        if ($slug == 'Inaugural Passes') {
            $allocationName = 'Inaugural Passes Allocated';
        }
    @endphp

    @if ($slug == 'Inaugural Passes')
        <div style="margin-top:5px; background: linear-gradient(90deg, #2196f3 0%, #21cbf3 100%); color: #0d2235; border-radius: 8px; padding: 16px 24px; margin-bottom: 18px; font-size: 1.08rem; font-weight: 500; box-shadow: 0 2px 8px rgba(33, 203, 243, 0.08); border-left: 6px solid #1976d2;">
            <span style="font-weight: bold;">Kindly note:</span>
            Registrations for the Inaugural Session will remain open until
            <span style="font-weight: bold; color: #0d47a1;">27-08-2025 05:00 PM</span>.
        </div>
    @endif
    <div class="container-fluid py-2">
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header">
                        <div class="text-left">
                            <h5 class="mb-0">@yield('title')</h5>
                            <p class="text-sm mb-0">
                                List of all registered Information.
                            </p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-info">{{ $allocationName }}:
                                    @if ($slug == 'Inaugural Passes')
                                        {{ $count['complimentary_delegate_count'] ?? 0 }}
                                    @elseif($slug == 'Stall Manning')
                                        {{ $count['stall_manning_count'] ?? 0 }}
                                    @endif

                                </span>
                                <span class="badge bg-success">Used:
                                    @if ($slug == 'Inaugural Passes')
                                        {{ $used['complimentary_delegates'] ?? 0 }}
                                    @elseif($slug == 'Stall Manning')
                                        {{ $used['stall_manning'] ?? 0 }}
                                    @endif
                                </span>
                            </div>
                            <div class="text-end">

                                @if($slug == 'Inaugural Passes')
                                @php $button = 'Invite / Add'; @endphp
                                @elseif($slug == 'Stall Manning')
                                @php $button = 'Invite'; @endphp
                                @endif

                                @if (
    $slug != 'Inaugural Passes' &&
    !(
        ($slug == 'Inaugural Passes' &&
            isset($count['complimentary_delegate_count'], $used['complimentary_delegates']) &&
            $used['complimentary_delegates'] >= $count['complimentary_delegate_count']) ||
        ($slug == 'Stall Manning' &&
            isset($count['stall_manning_count'], $used['stall_manning']) &&
            $used['stall_manning'] >= $count['stall_manning_count'])
    )
)
    <button type="button" class="btn btn-primary"
        onclick="openInviteModal('{{ $slug }}')">{{ $button }}</button>
    <button type="button" class="btn btn-secondary"
        onclick="openAddModal('{{ $slug }}')">Add</button>
@endif
                            </div>
                        </div>
                    </div>

                    {{-- show a guide only for Inaugural Passes to first add an email then you can fill out the information on their behalf as well --}}
                    
                    @if($slug == 'Inaugural Passes')
                        <div class="guide-box">
                            <div class="guide-header">
                                <span class="guide-icon">📋</span>
                                <strong>How to Use the Invite System</strong>
                            </div>
                            <div class="guide-content">
                                <ol>
                                    <li><strong>Step 1:</strong> Click the <span class="guide-highlight">"{{$button}}"</span> button above to invite someone via email</li>
                                    <li><strong>Step 2:</strong> Once invited, their email will appear in the table below</li>
                                    <li><strong>Step 3:</strong> Click on their <span class="guide-highlight">📧 Invite Link</span> or <span class="guide-highlight">Email</span> to fill out their information on their behalf</li>
                                    <li><strong>Step 4:</strong> After completing the form, their name will appear in the table</li>
                                </ol>
                            </div>
                        </div>
                    @endif
                    
                    @if($extractedData->where('token', '!=', null)->where('first_name', null)->count() > 0)

                        <div class="invite-note">
                            <span class="note-icon">💡</span>
                            <strong>Action Required:</strong> Some users have been invited but haven't completed their details yet. 
                            Click on the <span class="invite-link" style="padding: 2px 8px; font-size: 0.9em;">📧 Invite Link</span> 
                            or <span class="invite-link" style="padding: 2px 8px; font-size: 0.9em;">Email</span> 
                            to help them complete their registration.
                        </div>
                    @endif
                    
                    <div class="table-responsive">
                        <table class="table table-flush" id="datatable-basic">
                            <thead class="thead-light">
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        data-sort="first_name">Name</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        data-sort="email">Email</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        data-sort="role">Job title</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        data-sort="created_at">Mobile No</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"
                                        data-sort="created_at">Organisation Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($extractedData as $item)
                                    <tr>
                                        <td class="text-sm font-weight-normal">
                                            @if (!empty($item['first_name']) || !empty($item['last_name']))
                                                @if (!empty($item['unique_id']))
                                                    <a href="{{ route('exhibitor.pdf', ['id' => $item['unique_id']]) }}"
                                                        target="_blank">
                                                        {{ $item['first_name'] }} {{ $item['last_name'] }}
                                                    </a>
                                                @else
                                                    {{ $item['first_name'] }} {{ $item['last_name'] }}
                                                @endif
                                            @else
                                                @if (!empty($item['token']))
                                                    @if($slug == 'Inaugural Passes')
                                                        <a href="{{ route('exhibition.invited.inaugural', ['token' => $item['token']]) }}" target="_blank" class="invite-link">📧 Invite Link</a>
                                                    @else
                                                        <a href="{{ route('exhibition.invited', ['token' => $item['token']]) }}" target="_blank" class="invite-link">📧 Invite Link</a>
                                                    @endif
                                                @else
                                                    N/A
                                                @endif
                                            @endif
                                        </td>
                                        <td class="text-sm font-weight-normal">
                                            @if (!empty($item['token']) && empty($item['first_name']))
                                                @if($slug == 'Inaugural Passes')
                                                    <a href="{{ route('exhibition.invited.inaugural', ['token' => $item['token']]) }}" target="_blank" class="invite-link">{{ $item['email'] }}</a>
                                                @else
                                                    <a href="{{ route('exhibition.invited', ['token' => $item['token']]) }}" target="_blank" class="invite-link">{{ $item['email'] }}</a>
                                                @endif
                                            @else
                                                {{ $item['email'] }}
                                            @endif
                                        </td>
                                        <td class="text-sm font-weight-normal">{{ $item['job_title'] }}</td>
                                        <td class="text-sm font-weight-normal">{{ $item['mobile'] }}</td>
                                        <td class="text-sm font-weight-normal">
                                            {{ $item['organisation_name'] ?: $companyName }}</td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination-container mt-3 text-end me-5"></div>
                </div>
            </div>
        </div>
        @if ($slug == 'Inaugural Passes')
            <div class="alert alert-warning mt-3" role="alert">
                <strong>Note:</strong> Kindly note that participation (in-person) in the Inaugural event is subject to final
                confirmation and will be informed separately from 3rd week of August onwards.
            </div>
        @endif

    </div>

    @if ($slug == 'Inaugural Passes')
        @php
            $input_type = 'delegate';
            $input_type2 = 'delegate';
        @endphp
    @elseif($slug == 'Stall Manning')
        @php
            $input_type = 'exhibitor';
            $input_type2 = 'exhibitor';
        @endphp
    @endif

    <!-- Invite Modal -->

    <div class="modal fade" id="inviteModal" tabindex="-1" aria-labelledby="inviteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="inviteModalLabel">Invite </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="inviteForm">
                        <div class="mb-3">
                            <input type="hidden" id="csrfToken" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="invite_type" id="inviteType" value="{{ $input_type2 }}">
                            <label for="inviteEmail" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="inviteEmail" name="email" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Invite</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if ($slug != 'Inaugural Passes')
        <!-- Add Modal -->
        <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addModalLabel">Add {{ $slug }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addForm">
                            <input type="hidden" id="csrfToken" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="invite_type" id="inviteType2" value="{{ $input_type }}">
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" class="form-control" id="phone" required>
                                <input type="hidden" id="fullPhoneNumber" name="fullPhoneNumber">
                            </div>
                            <div class="mb-3">
                                <label for="organisationName" class="form-label">Organisation Name</label>
                                <input type="text" class="form-control" id="organisationName" name="organisationName"
                                    value="{{ $companyName }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="jobTitle" class="form-label">Job Title</label>
                                <input type="text" class="form-control" id="jobTitle" required>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="idCardType" class="form-label">ID Card Type</label>
                                    <select class="form-control" id="idCardType" required>
                                        <option value="">Select ID Card Type</option>
                                        <option value="Aadhar Card">Aadhar Card</option>
                                        <option value="Pan Card">PAN Card</option>
                                        <option value="Driving License">Driving License</option>
                                        <option value="Passport">Passport</option>
                                        <option value="Voter ID">Voter ID</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="idCardNumber" class="form-label">ID Card Number</label>
                                    <input type="text" class="form-control" id="idCardNumber" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-secondary">Add {{ $slug }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif


    <script>
        $(document).ready(function() {
            var input = document.querySelector("#phone");
            var iti = window.intlTelInput(input, {
                initialCountry: "auto",
                geoIpLookup: function(callback) {
                    $.get("https://ipinfo.io", function() {}, "jsonp").always(function(resp) {
                        var countryCode = resp && resp.country ? resp.country : "us";
                        console.log("Country Code:", countryCode);
                        callback(countryCode);
                    });
                },
                separateDialCode: true,
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
            });

            // make palceholder empty
            input.placeholder = '';


            function updatePhoneNumber() {
                var fullNumber = iti.getNumber();
                var countryData = iti.getSelectedCountryData();
                var nationalNumber = iti.getNumber(intlTelInputUtils.numberFormat.NATIONAL).replace(/\s/g, '')
                    .replace(/^0+/, '');

                if (iti.isValidNumber()) {
                    var formattedNumber = `+${countryData.dialCode}-${nationalNumber}`; // Add separator '-'
                    $("#fullPhoneNumber").val(formattedNumber);
                    console.log("Updated Full Phone Number:", formattedNumber);
                } else {
                    $("#fullPhoneNumber").val(""); // Reset if invalid
                }
            }

            // Trigger update whenever user types or changes country
            $("#phone").on("change keyup", updatePhoneNumber);
            $(".iti__country-list li").on("click", updatePhoneNumber); // Ensure update on country change

            // Ensure phone number is valid before form submission
            $("#addForm").on("submit", function(event) {
                event.preventDefault();

                updatePhoneNumber(); // Ensure latest value before submit

                var fullPhoneNumber = $("#fullPhoneNumber").val();
                if (!fullPhoneNumber) {
                    Swal.fire('Error', 'Please enter a valid phone number.', 'error');
                    return;
                }

                const formData = new FormData();
                formData.append('_token', document.getElementById('csrfToken').value);
                formData.append('name', document.getElementById('name').value);
                formData.append('email', document.getElementById('email').value);
                formData.append('phone', fullPhoneNumber);
                formData.append('jobTitle', document.getElementById('jobTitle').value);
                formData.append('idCardType', document.getElementById('idCardType').value);
                formData.append('idCardNumber', document.getElementById('idCardNumber').value);
                formData.append('invite_type', document.getElementById('inviteType2').value);
                formData.append('organisationName', document.getElementById('organisationName').value);

                fetch('/add', {
                        method: 'POST',
                        body: formData

                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            Swal.fire('Error', JSON.stringify(data.error), 'error');
                        } else {
                            Swal.fire('Success', data.message, 'success');
                            var addModal = bootstrap.Modal.getInstance(document.getElementById(
                                'addModal'));
                            addModal.hide();
                            location.reload();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Something went wrong! ' + error.message, 'error');
                    });


            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var phoneInput = document.getElementById('phone');
            if (phoneInput) {
                setTimeout(function() {
                    phoneInput.removeAttribute('placeholder');
                }, 100);
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function openInviteModal(slug) {
            var inviteModal = new bootstrap.Modal(document.getElementById('inviteModal'));
            inviteModal.show();
        }

        function openAddModal(slug) {
            var addModal = new bootstrap.Modal(document.getElementById('addModal'));
            addModal.show();
        }

        document.getElementById('inviteForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = {
                email: document.getElementById('inviteEmail').value,
                invite_type: document.getElementById('inviteType').value,
                _token: document.getElementById('csrfToken').value, // CSRF Token
            };

            fetch('/invite', {
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
                        Swal.fire('Error', JSON.stringify(data.error), 'error');
                    } else {
                        Swal.fire('Success', data.message, 'success');
                        var inviteModal = bootstrap.Modal.getInstance(document.getElementById('inviteModal'));
                        inviteModal.hide();
                        location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Something went wrong! ' + error.message, 'error');
                });
        });
    </script>

    <script>
        $(document).ready(function() {
            var modal = bootstrap.Modal.getInstance(document.getElementById('addModal'));

            // Reset form fields when modal is closed
            $('#addModal').on('hidden.bs.modal', function() {
                $('#addForm')[0].reset();
                document.getElementById('jobTitle').value = '';
                document.getElementById('idCardType').value = '';
                document.getElementById('idCardNumber').value = '';
                modal.hide();
            });
        });
    </script>



@endsection
