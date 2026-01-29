@extends('layouts.dashboard')
@section('title', 'Complimentary Delegates')
@section('content')

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
                    const response = await fetch(`/exhibitor/list/complimentary?page=${page}&sort=${sortField}&direction=${sortDirection}&per_page=${perPage}`, {
                        headers: { 'Accept': 'application/json' }
                    });
                    if (!response.ok) throw new Error('Failed to fetch users');

                    const data = await response.json();
                    console.log(data);
                    renderTable(data.data);
                    renderPagination(data);
                } catch (error) {
                    console.error('Error fetching users:', error);
                }
            }

            function renderTable(users) {
                tableBody.innerHTML = '';
                users.forEach(user => {
                    const row = `
                <tr>
                    <td class="text-sm font-weight-normal">${user.first_name} ${user.last_name || ''}</td>
                    <td class="text-sm font-weight-normal">${user.email}</td>
                    <td class="text-sm font-weight-normal">${user.role || 'N/A'}</td>
                    <td class="text-sm font-weight-normal">${new Date(user.created_at).toLocaleDateString()}</td>
                    <td class="text-sm font-weight-normal">
                        <button class="btn btn-sm btn-primary" onclick="showConfirmationModal('${user.email}', '${user.first_name}')">Action</button>
                    </td>
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
                        <div class="text-end">
                            <button type="button" class="btn btn-primary" onclick="openInviteModal()">Invite</button>
                            <button type="button" class="btn btn-secondary" onclick="openAddModal()">Add</button>
                        </div>
                    </div>
{{--                    <div class="card-body dataTable-dropdown">--}}
{{--                        <!-- Per Page Selector -->--}}
{{--                        <label>--}}
{{--                            <select id="perPage" class=" dataTable-selector">--}}
{{--                                <option value="5">5</option>--}}
{{--                                <option value="10" selected>10</option>--}}
{{--                                <option value="25">25</option>--}}
{{--                                <option value="50">50</option>--}}
{{--                            </select>--}}
{{--                            Rows per page--}}
{{--                        </label>--}}
{{--                    </div>--}}
                    <div class="table-responsive">
                        <table class="table table-flush" id="datatable-basic">
                            <thead class="thead-light">
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" data-sort="name">Name</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" data-sort="email">Email</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" data-sort="user_type">User Type</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" data-sort="created_at">Created at</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div class="pagination-container mt-3 text-end me-5"></div>
                </div>
            </div>
        </div>
    </div>

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
                            <input type="hidden" name="invite_type" id="inviteType" value="delegate">
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
                    <h5 class="modal-title" id="addModalLabel">Add Delegate</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="countryCode" class="form-label">Country Code</label>
                            <input type="text" class="form-control" id="countryCode" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input type="text" class="form-control" id="phone" required>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function openInviteModal() {
            var inviteModal = new bootstrap.Modal(document.getElementById('inviteModal'));
            inviteModal.show();
        }

        function openAddModal() {
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
                .then(response => {
                    if (!response.ok) {
                        // If response is not OK, check if it's JSON
                        return response.text().then(text => {
                            try {
                                return JSON.parse(text);
                            } catch {
                                throw new Error('Server returned an invalid response');
                            }
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        Swal.fire('Error', JSON.stringify(data.error), 'error');
                    } else {
                        Swal.fire('Success', data.message, 'success');
                        var inviteModal = bootstrap.Modal.getInstance(document.getElementById('inviteModal'));
                        inviteModal.hide();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire('Error', 'Something went wrong! ' + error.message, 'error');
                });
        });



        document.getElementById('addForm').addEventListener('submit', function(event) {
            event.preventDefault();
            const formData = {
                name: document.getElementById('name').value,
                email: document.getElementById('email').value,
                countryCode: document.getElementById('countryCode').value,
                phone: document.getElementById('phone').value,
                jobTitle: document.getElementById('jobTitle').value
            };

            fetch('/add-delegate', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(formData)
            })
                .then(response => response.json())
                .then(data => {
                    Swal.fire('Success', data.message, 'success');
                    var addModal = bootstrap.Modal.getInstance(document.getElementById('addModal'));
                    addModal.hide();
                })
                .catch(error => Swal.fire('Error', 'Something went wrong!', 'error'));
                console.error('Error:', error);
        });
    </script>


@endsection


