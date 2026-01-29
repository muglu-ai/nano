@extends('layouts.users')
@section('title', $slug)
@section('content')
    <!-- intlTelInput Plugin -->

    @php
    if($slug == "Complimentary Delegates") {
        $link = "complimentary";
    } elseif($slug == "Stall Manning") {
        $link = "stall_manning";
    }
    @endphp


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tableBody = document.querySelector('#datatable-basic tbody');
            const perPageSelector = document.querySelector('#perPage');
            const paginationContainer = document.querySelector('.pagination-container');
            let sortField = 'first_name'; // Default sorting by first_name
            let sortDirection = 'asc';
            let perPage = 10;
            async function fetchUsers(page = 1) {
                console.log(`Fetching users for page: ${page}`);
                try {
                    const response = await fetch(`/exhibitor/list/${link}?page=${page}&sort=${sortField}&direction=${sortDirection}&per_page=${perPage}`, {
                        headers: { 'Accept': 'application/json' }
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
                    const row = `
            <tr>
                <td class="text-sm font-weight-normal">${user.first_name} ${user.last_name || ''}</td>
                <td class="text-sm font-weight-normal">${user.email}</td>
                <td class="text-sm font-weight-normal">${user.job_title || 'N/A'}</td>
                <td class="text-sm font-weight-normal">${new Date(user.created_at).toLocaleDateString()}</td>

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
                    button.addEventListener('click', function () {
                        fetchUsers(this.dataset.page);
                    });
                });
            }

            // Sorting headers
            document.querySelectorAll('.thead-light th').forEach(header => {
                header.addEventListener('click', function () {
                    const field = this.dataset.sort;
                    if (field) {
                        sortField = field;
                        sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                        fetchUsers();
                    }
                });
            });

            // Per page selector
            perPageSelector.addEventListener('change', function () {
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
            ];
        });
       // dd($extractedData);
    @endphp
    <div class="container-fluid py-2">
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header">
                        <div class="text-left">
                            <h5 class="mb-0">@yield('title')</h5>
                            <p class="text-sm mb-0">
                                List of all registered users.
                            </p>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-info">Allocated:
                                    @if($slug == 'Complimentary Delegates')
                                        {{ $count['complimentary_delegate_count'] ?? 0 }}
                                    @elseif($slug == 'Stall Manning')
                                        {{ $count['stall_manning_count'] ?? 0 }}
                                    @endif

                                </span>
                                <span class="badge bg-success">Used:
                                    @if($slug == 'Complimentary Delegates')
                                        {{ $used['complimentary_delegates'] ?? 0 }}
                                    @elseif($slug == 'Stall Manning')
                                        {{ $used['stall_manning'] ?? 0 }}
                                    @endif</span>
                            </div>
                            <div class="text-end">
                                <button type="button" class="btn btn-primary" onclick="openInviteModal('{{ $slug }}')">Invite</button>
                                <button type="button" class="btn btn-secondary" onclick="openAddModal('{{ $slug }}')">Add</button>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-flush" id="datatable-basic">
                            <thead class="thead-light">
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" data-sort="first_name">Name</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" data-sort="email">Email</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" data-sort="role">Job title</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" data-sort="created_at">Created at</th>
{{--                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status </th>--}}
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($extractedData as $item)
                                <tr>
                                    <td class="text-sm font-weight-normal">{{ $item['first_name'] }} {{ $item['last_name'] }}</td>
                                    <td class="text-sm font-weight-normal">{{ $item['email'] }}</td>
                                    <td class="text-sm font-weight-normal">{{ $item['job_title'] }}</td>
                                    <td class="text-sm font-weight-normal">{{ $item['mobile'] }}</td>
                                    <td class="text-sm font-weight-normal">{{ $item['organisation_name'] }}</td>
{{--                                    <td class="text-sm font-weight-normal">--}}
{{--                                    @php--}}
{{--                                    if ($item['token'] != null) {--}}
{{--                                        echo '<button class="btn btn-sm btn-primary">Invitation Pending</button>';--}}
{{--                                    } else {--}}
{{--                                        echo '<button class="btn btn-sm btn-primary">Invited</button>';--}}
{{--                                    }--}}
{{--                                    @endphp--}}
{{--                                        <button class="btn btn-sm btn-primary">Invited</button>--}}
{{--                                    </td>--}}
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="pagination-container mt-3 text-end me-5"></div>
                </div>
            </div>
        </div>
    </div>

    @if($slug == "Complimentary Delegates")
        @php
            $input_type = "delegate";
            $input_type2 = "delegate"
        @endphp
    @elseif($slug == "Stall Manning")
        @php
            $input_type = "exhibitor";
            $input_type2 = "exhibitor";
        @endphp
    @endif

    <!-- Invite Modal -->
    <div class="modal fade" id="inviteModal" tabindex="-1" aria-labelledby="inviteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="inviteModalLabel">Invite Delegate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="inviteForm">
                        <div class="mb-3">
                            <input type="hidden" id="csrfToken" name="_token" value="{{ csrf_token() }}">
                            <input type="hidden" name="invite_type" id="inviteType" value="{{$input_type2}}">
                            <label for="inviteEmail" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="inviteEmail" name="email" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Send Invite</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel">Add {{$slug}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addForm">
                        <input type="hidden" id="csrfToken" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="invite_type" id="inviteType2" value="{{$input_type}}">
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
                            <label for="jobTitle" class="form-label">Job Title</label>
                            <input type="text" class="form-control" id="jobTitle" required>
                        </div>
                        <button type="submit" class="btn btn-secondary">Add Delegate</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            var input = document.querySelector("#phone");
            var iti = window.intlTelInput(input, {
                initialCountry: "auto",
                geoIpLookup: function (callback) {
                    $.get("https://ipinfo.io", function () {}, "jsonp").always(function (resp) {
                        var countryCode = resp && resp.country ? resp.country : "us";
                        callback(countryCode);
                    });
                },
                separateDialCode: true,
                utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
            });

            function updatePhoneNumber() {
                var fullNumber = iti.getNumber();
                var countryData = iti.getSelectedCountryData();
                var nationalNumber = iti.getNumber(intlTelInputUtils.numberFormat.NATIONAL).replace(/\s/g, '').replace(/^0+/, '');

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
            $("#addForm").on("submit", function (event) {
                event.preventDefault();

                updatePhoneNumber(); // Ensure latest value before submit

                var fullPhoneNumber = $("#fullPhoneNumber").val();
                if (!fullPhoneNumber) {
                    Swal.fire('Error', 'Please enter a valid phone number.', 'error');
                    return;
                }

                const formData = {
                    _token: document.getElementById('csrfToken').value,
                    name: document.getElementById('name').value,
                    email: document.getElementById('email').value,
                    phone: fullPhoneNumber,
                    jobTitle: document.getElementById('jobTitle').value,
                    invite_type : document.getElementById('inviteType2').value
                };

                console.log("Submitting Form Data: ", formData);

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
                            Swal.fire('Error', JSON.stringify(data.error), 'error');
                        } else {
                            Swal.fire('Success', data.message, 'success');
                            var addModal = bootstrap.Modal.getInstance(document.getElementById('addModal'));
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

@endsection
