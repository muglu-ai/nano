@extends('layouts.dashboard')
@section('title', 'All Sponsorship Application')
@section('content')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const tableBody = document.querySelector('#datatable-basic tbody');
            const paginationContainer = document.querySelector('.pagination-container');
            let currentPage = 1;
            let sortField = 'billing_company';
            let sortDirection = 'asc';

            async function fetchSponsors(page = 1) {
                try {
                    const response = await fetch(`/sponsors_list?page=${page}&sort=${sortField}&direction=${sortDirection}`);
                    const url = `/sponsors_list?page=${page}&sort=${sortField}&direction=${sortDirection}`;
                    const data = await response.json();
                    renderTable(data.data);
                    renderPagination(data);
                    currentPage = data.current_page;
                } catch (error) {
                    console.error('Error fetching sponsors:', error);
                }
            }

            function renderTable(sponsors) {
                tableBody.innerHTML = '';

                sponsors.forEach(sponsor => {
                    const sponsorId = sponsor.id;
                    const applicationId = sponsor.application_id;
                    const companyName = sponsor.application?.billing_detail?.billing_company || 'N/A';
                    const sponsorshipItem = sponsor.sponsorship_item || 'N/A';
                    const contactName = sponsor.application?.event_contact
                        ? `${sponsor.application.event_contact.first_name} ${sponsor.application.event_contact.last_name}`
                        : 'N/A';
                    const contactEmail = sponsor.application?.event_contact?.email || 'N/A';
                    const contactNo = sponsor.user?.email || 'N/A'; // Assuming email as contact
                    const applicationStatus = sponsor.status || 'N/A';

                    const row = document.createElement('tr');
                    row.innerHTML = `
                <td class="text-sm font-weight-normal">${companyName}</td>
                <td class="text-sm font-weight-normal">${sponsorshipItem}</td>
                <td class="text-sm font-weight-normal">
                    <div class="d-flex flex-column justify-content-center">
                        <h6 class="mb-0 text-xxs">${contactName}</h6>
                        <p class="text-xs text-secondary mb-0">${contactEmail}</p>
                    </div>
                </td>
                <td class="text-sm font-weight-normal">${contactNo}</td>
                <td class="text-sm font-weight-normal">${applicationStatus}</td>
                <td class="text-sm font-weight-normal">
                    <button class="btn btn-sm btn-primary action-btn" data-id="${sponsorId}" data-app_id="${applicationId}">Action</button>
                </td>
            `;
                    tableBody.appendChild(row);
                });
            }

            function renderPagination(data) {
                paginationContainer.innerHTML = '';

                data.links.forEach(link => {
                    if (link.url) {
                        const btn = document.createElement('button');
                        btn.className = `btn btn-sm ${link.active ? 'btn-primary' : 'btn-secondary'}`;
                        btn.dataset.page = getPageNumberFromUrl(link.url);
                        btn.innerHTML = link.label;
                        paginationContainer.appendChild(btn);
                    }
                });
            }

            function getPageNumberFromUrl(url) {
                const urlParams = new URL(url, window.location.origin).searchParams;
                return urlParams.get('page');
            }

            // Event Delegation for Pagination Clicks
            paginationContainer.addEventListener('click', function (event) {
                if (event.target.tagName === 'BUTTON') {
                    fetchSponsors(event.target.dataset.page);
                }
            });

            // Sorting Headers
            document.querySelectorAll('.thead-light th[data-sort]').forEach(header => {
                header.addEventListener('click', function () {
                    sortField = this.dataset.sort;
                    sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
                    fetchSponsors();
                });
            });

            // Event Delegation for Action Buttons
            tableBody.addEventListener('click', function (event) {
                if (event.target.classList.contains('action-btn')) {
                    const sponsorId = event.target.dataset.id;
                    const applicationId = event.target.dataset.app_id;
                    showConfirmationModal(sponsorId, applicationId);
                }
            });

            // Initial Fetch
            fetchSponsors();
        });


        function showConfirmationModal(sponsorId, applicationId) {
            // Retrieve the modal element
            const modalElement = document.getElementById('confirmationModal');

            // Ensure modal exists
            if (!modalElement) {
                console.error("Modal element not found!");
                return;
            }

            // Set the values inside the modal
            document.querySelector('#confirmationModal .modal-body').innerHTML =
                `<p>Are you sure you want to approve the sponsorship application for ID: <strong>${applicationId}</strong>?</p>`;

            // Store the sponsorId in a data attribute to be used later in the confirmation function
            document.querySelector('#confirmationModal .btn-primary').setAttribute('data-id', sponsorId);
            document.querySelector('#confirmationModal .btn-primary').setAttribute('data-app_id', applicationId);

            // Initialize and show the modal using Bootstrap
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        }
        function approveSponsorship() {
            const sponsorId = document.querySelector('#confirmationModal .btn-primary').getAttribute('data-id');

            const applicationId = document.querySelector('#confirmationModal .btn-primary').getAttribute('data-app_id');
            fetch(`http://127.0.0.1:8000/approve-sponsorship`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    sponsor_id: sponsorId,
                    app_id : applicationId

                })
            })

                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Sponsorship approved successfully.');
                        location.reload(); // Refresh the page
                    } else {
                        //console.log(data);
                        alert('Failed to approve sponsorship.');
                    }
                })
                .catch(error => console.error('Error approving sponsorship:', error));
        }
    </script>
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Approve Sponsorship Application</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Requested sponsorship for </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="approveSponsorship()">Confirm</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-2">
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header">
                        <h5 class="mb-0">@yield('title')</h5>
                        <p class="text-sm mb-0">
                            List of all sponsorship application.
                        </p>
                    </div>
                    <div class="card-body dataTable-dropdown">
                        <!-- Per Page Selector -->
                        <label>
                            <select id="perPage" class=" dataTable-selector">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                            Rows per page
                        </label>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-flush" id="datatable-basic">
                            <thead class="thead-light">
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" data-sort="billing_company">Company Name</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" data-sort="sponsorship_item">Sponsor Item</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" data-sort="billing_company">
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-xxs">Name</h6>
                                        <p class="text-xs text-secondary mb-0">Email</p>
                                    </div>
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" data-sort="billing_company">Contact No</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" data-sort="status">Application Status</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Action</th>
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
