@extends('layouts.dashboard')
@section('title', 'All Registered Users')
@section('content')

    <style>
        thead.custom-header {
            background-color: #000; /* Light gray */
            color: #fff; /* Dark text */
        }
        th {
            text-align: left !important;
            padding-left:20px !important;
            color:white !important;
        }
        .dataTable-table th a {
            text-decoration: none;
            color: white;
        }

    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tableBody = document.querySelector('#datatable-basic tbody');
            const perPageSelector = document.querySelector('#perPage');
            const paginationContainer = document.querySelector('.pagination-container');
            let sortField = 'name';
            let sortDirection = 'asc';
            let perPage = 10;

            async function fetchUsers(page = 1) {
                //console.log(`Fetching users for page: ${page}`);
                // make this as route based as with name users.list this should be used as php  route('users.list')
                const url = "{{ route('users.list') }}";
                
                try {
                    const response = await fetch(`${url}?page=${page}&sort=${sortField}&direction=${sortDirection}&per_page=${perPage}`);
                    
                    const data = await response.json();
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
                            <td class="text-md font-weight-normal text-dark">${user.name}</td>
                            <td class="text-md font-weight-normal text-dark">${user.email}</td>
                            <td class="text-md font-weight-normal text-dark">${user.simplePass}</td>
                            <td class="text-md font-weight-normal text-dark">${new Date(user.created_at).toLocaleDateString()}</td>
                            <td class="text-md font-weight-normal text-dark">
                                <button class="btn btn-md btn-info text-uppercase" onclick="showConfirmationModal('${user.email}', '${user.name}')">Action</button>
                            </td>
                        </tr>`;
                    tableBody.innerHTML += row;
                });
            }

            function renderPagination(data) {
                paginationContainer.innerHTML = '';
                for (let i = 1; i <= data.last_page; i++) {
                    paginationContainer.innerHTML += `
                        <button class="btn btn-sm ${data.current_page === i ? 'btn-info' : 'btn-secondary'}"
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
                    sortField = this.dataset.sort;
                    sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                    fetchUsers();
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
        function showConfirmationModal(email, name) {
            // Set the email and name in the modal
            document.querySelector('#confirmation-email').textContent = email;
            document.querySelector('#confirmation-name').textContent = name;

            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
            modal.show();
        }
        function upgradeToAdmin2() {
            const email = document.querySelector('#confirmation-email').textContent;

            // Send an API request to upgrade the user
            fetch(``, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ email })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('User upgraded to Admin successfully.');
                        location.reload(); // Reload the page to refresh the user list
                    } else {
                        alert('Failed to upgrade user.');
                    }
                })
                .catch(error => console.error('Error upgrading user:', error));
        }
    </script>

    {{-- <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Upgrade User to Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to upgrade <strong id="confirmation-name"></strong> (<span id="confirmation-email"></span>) to Admin?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="upgradeToAdmin()">Confirm</button>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="container-fluid py-2">
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header">
                        <h5 class="mb-0">@yield('title')</h5>
                        <p class="text-sm mb-0 text-dark">
                            List of all registered users.
                        </p>
                    </div>
                    <div class="card-body dataTable-dropdown">
                        <!-- Per Page Selector -->
                        <label class="text-dark">
                        <select id="perPage" class=" dataTable-selector">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        Rows per page
                        </label>
                    </div>

                    <div class="table-responsive min-vh-40" style="height: 500px;">
                        <table class="table table-flush min-vh-40" id="datatable-basic">
                            <thead class="thead-light table-dark custom-header">
                            <tr>
                                <th class="text-uppercase text-md text-white" data-sort="name">Name</th>
                                <th class="text-uppercase text-md text-white" data-sort="email">Email</th>
                                <th class="text-uppercase text-md text-white" data-sort="phone">Password</th>
                                <th class="text-uppercase text-md text-white" data-sort="created_at">Created at</th>
                                <th class="text-uppercase text-md text-white" data-sort="name">Action</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <!-- Pagination -->

                    <div class="pagination-container mt-3 text-end me-5"></div>
                </div>
            </div>
        </div>
    </div>
@endsection
