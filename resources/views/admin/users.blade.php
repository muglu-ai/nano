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
            const tableBody = document.querySelector('#datatable-basic2 tbody');
            const perPageSelector = document.querySelector('#perPage');
            const paginationContainer = document.querySelector('.pagination-container');
            let sortField = 'name';
            let sortDirection = 'asc';
            let perPage = 10;

            async function fetchUsers(page = 1) {
                //console.log(`Fetching users for page: ${page}`);
                // make this as route based as with name users.list this should be used as php  route('users.list')
                const url = "{{ route('getUsers2') }}";
                
                try {
                    const response = await fetch(url + `?page=${page}&sort=${sortField}&direction=${sortDirection}&per_page=${perPage}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    
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
                    const portalUrl = '{{ env("APP_URL") }}';
                    const row = `
                        <tr>
                            <td class="text-md font-weight-normal text-dark">${user.name}</td>
                            <td class="text-md font-weight-normal text-dark">${user.email}</td>
                            <td class="text-md font-weight-normal text-dark">${user.simplePass}</td>
                            <td class="text-md font-weight-normal text-dark">
                                <button class="btn btn-sm btn-primary" onclick="copyCredentials('${portalUrl}', '${user.email}', '${user.simplePass}', '${user.name}')">
                                    <i class="fas fa-copy"></i> Copy Credentials
                                </button>
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
        function copyCredentials(portalUrl, username, password, userName) {
            const credentials = `Portal URL: ${portalUrl}\nUsername: ${username}\nPassword: ${password}`;
            
            // Use the modern Clipboard API if available
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(credentials).then(() => {
                    showNotification(`Credentials copied for ${userName}`, 'success');
                }).catch(err => {
                    console.error('Failed to copy: ', err);
                    fallbackCopyTextToClipboard(credentials, userName);
                });
            } else {
                // Fallback for older browsers
                fallbackCopyTextToClipboard(credentials, userName);
            }
        }

        function fallbackCopyTextToClipboard(text, userName) {
            const textArea = document.createElement("textarea");
            textArea.value = text;
            
            // Avoid scrolling to bottom
            textArea.style.top = "0";
            textArea.style.left = "0";
            textArea.style.position = "fixed";
            textArea.style.opacity = "0";

            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                const successful = document.execCommand('copy');
                if (successful) {
                    showNotification(`Credentials copied for ${userName}`, 'success');
                } else {
                    showNotification(`Failed to copy credentials for ${userName}`, 'error');
                }
            } catch (err) {
                console.error('Fallback: Oops, unable to copy', err);
                showNotification(`Failed to copy credentials for ${userName}`, 'error');
            }

            document.body.removeChild(textArea);
        }

        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'success' ? 'success' : type === 'error' ? 'danger' : 'info'} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            // Add to page
            document.body.appendChild(notification);
            
            // Auto remove after 3 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 3000);
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
                        <table class="table table-flush min-vh-40" id="datatable-basic2">
                            <thead class="thead-light table-dark custom-header">
                            <tr>
                                <th class="text-uppercase text-md text-white" data-sort="name">Name</th>
                                <th class="text-uppercase text-md text-white" data-sort="email">Email</th>
                                <th class="text-uppercase text-md text-white" data-sort="phone">Password</th>
                                <th class="text-uppercase text-md text-white">Action</th>
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
