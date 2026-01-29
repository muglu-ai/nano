@extends('layouts.dashboard')
@section('title', 'All Registered Users')
@section('content')

    <style>
        /* Clean and simple design */
        .card {
            border: 1px solid #e3e6f0;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        }
        
        .card-header {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            color: #5a5c69;
        }
        
        .card-header h5 {
            color: #5a5c69;
            font-weight: 600;
        }
        
        /* Search section */
        .search-section {
            background-color: #f8f9fc;
            border-bottom: 1px solid #e3e6f0;
            padding: 1.5rem;
        }
        
        .search-input {
            border: 2px solid #d1d3e2;
            border-radius: 0.35rem;
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }
        
        .search-input:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        .search-btn {
            background-color: #4e73df;
            border-color: #4e73df;
            border-radius: 0.35rem;
            padding: 0.75rem 1.5rem;
        }
        
        .search-btn:hover {
            background-color: #2e59d9;
            border-color: #2e59d9;
        }
        
        .clear-btn {
            background-color: #e74a3b;
            border-color: #e74a3b;
            border-radius: 0.35rem;
            padding: 0.75rem 1.5rem;
        }
        
        .clear-btn:hover {
            background-color: #c0392b;
            border-color: #c0392b;
        }
        
        /* Table styling */
        .table th {
            background-color: #5a5c69;
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding: 1rem 0.75rem;
            border: none;
        }
        
        .table td {
            padding: 0.75rem;
            vertical-align: middle;
            border-top: 1px solid #e3e6f0;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fc;
        }
        
        /* Sortable headers */
        .sortable {
            cursor: pointer;
            position: relative;
        }
        
        .sortable:hover {
            background-color: #6c757d !important;
        }
        
        .sortable::after {
            content: '↕';
            position: absolute;
            right: 8px;
            opacity: 0.5;
        }
        
        /* Pagination */
        .pagination {
            margin: 0;
        }
        
        .page-link {
            color: #4e73df;
            border: 1px solid #d1d3e2;
            padding: 0.5rem 0.75rem;
            margin: 0 2px;
            border-radius: 0.35rem;
        }
        
        .page-item.active .page-link {
            background-color: #4e73df;
            border-color: #4e73df;
            color: white;
        }
        
        .page-link:hover {
            color: #2e59d9;
            background-color: #f8f9fc;
            border-color: #d1d3e2;
        }
        
        /* Copy credentials button */
        .copy-btn {
            background-color: #e83e8c;
            border-color: #e83e8c;
            color: white;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 0.35rem;
        }
        
        .copy-btn:hover {
            background-color: #d91a72;
            border-color: #d91a72;
            color: white;
        }

        /* Send credentials button */
        .send-credentials-btn {
            background-color: #28a745;
            border-color: #28a745;
            color: white;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 0.35rem;
        }

        .send-credentials-btn:hover:not(:disabled) {
            background-color: #218838;
            border-color: #218838;
            color: white;
        }

        .send-credentials-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-group {
            gap: 0.25rem;
        }
        
        /* Results info */
        .results-info {
            color: #5a5c69;
            font-size: 0.9rem;
        }
        
        .filter-badge {
            background-color: #4e73df;
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            font-size: 0.75rem;
        }
    </style>

    <div class="container-fluid py-2">
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-0">@yield('title')</h5>
                                <p class="text-sm mb-0">
                                    List of all registered users.
                                </p>
                            </div>
                            <div class="text-end">
                                <span class="badge bg-primary">
                                    <i class="fas fa-users me-1"></i>
                                    {{ $users->total() }} Users
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Search Section -->
                    <div class="search-section">
                        <form method="GET" action="{{ route('users.list') }}">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control search-input" 
                                               name="search" 
                                               placeholder="Search by name, email, or company..." 
                                               value="{{ request('search') }}">
                                        <button class="btn search-btn" type="submit">
                                            <i class="fas fa-search me-1"></i> Search
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <select name="per_page" class="form-control search-input" onchange="this.form.submit()">
                                        <option value="10" {{ request('per_page') == 10 || !request('per_page') ? 'selected' : '' }}>10 per page</option>
                                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 per page</option>
                                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 per page</option>
                                    </select>
                                </div>
                                <div class="col-md-2 text-end">
                                    @if(request('search'))
                                        <a href="{{ route('users.list') }}" class="btn clear-btn">
                                            <i class="fas fa-times me-1"></i> Clear
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th class="sortable" onclick="sortTable('company')">
                                    Company
                                    @if(request('sort') == 'company')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </th>
                                <th class="sortable" onclick="sortTable('name')">
                                    Name
                                    @if(request('sort') == 'name')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </th>
                                <th class="sortable" onclick="sortTable('email')">
                                    Email
                                    @if(request('sort') == 'email')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }} ms-1"></i>
                                    @endif
                                </th>
                                <th>Password</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $user->company ?? 'N/A' }}</td>
                                        <td>{{ $user->name ?? 'N/A' }}</td>
                                        <td>{{ $user->email ?? 'N/A' }}</td>
                                        <td>{{ $user->simplePass ?? 'N/A' }}</td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button class="btn copy-btn" 
                                                        onclick="copyCredentials('{{ env("APP_URL") }}', '{{ $user->email }}', '{{ $user->simplePass }}', '{{ $user->name }}', '{{ $user->company }}')">
                                                    <i class="fas fa-copy me-1"></i> Copy Credentials
                                                </button>
                                                <button class="btn btn-success send-credentials-btn" 
                                                        data-user-id="{{ $user->id }}"
                                                        data-user-name="{{ $user->name }}"
                                                        data-user-email="{{ $user->email }}">
                                                    <i class="fas fa-paper-plane me-1"></i> Resend Credentials
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            @if(request('search'))
                                                <i class="fas fa-search me-2"></i>
                                                No users found matching your search criteria.
                                            @else
                                                <i class="fas fa-users me-2"></i>
                                                No users found.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="results-info">
                                <i class="fas fa-info-circle me-1"></i>
                                Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} results
                                @if(request('search'))
                                    <span class="filter-badge ms-2">
                                        <i class="fas fa-search me-1"></i>
                                        Filtered
                                    </span>
                                @endif
                            </div>
                            <div>
                                {{ $users->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function sortTable(field) {
            const currentSort = '{{ request("sort") }}';
            const currentDirection = '{{ request("direction") }}';
            const newDirection = (currentSort === field && currentDirection === 'asc') ? 'desc' : 'asc';
            
            const url = new URL(window.location);
            url.searchParams.set('sort', field);
            url.searchParams.set('direction', newDirection);
            window.location.href = url.toString();
        }

        function copyCredentials(portalUrl, username, password, userName, companyName) {
            const credentials = `Company: ${companyName}\nContact: ${userName}\nPortal URL: ${portalUrl}\nUsername: ${username}\nPassword: ${password}`;
            
            // Use the modern Clipboard API if available
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(credentials).then(() => {
                    showNotification(`Credentials copied for ${userName} (${companyName})`, 'success');
                }).catch(err => {
                    console.error('Failed to copy: ', err);
                    fallbackCopyTextToClipboard(credentials, userName, companyName);
                });
            } else {
                // Fallback for older browsers
                fallbackCopyTextToClipboard(credentials, userName, companyName);
            }
        }

        function fallbackCopyTextToClipboard(text, userName, companyName) {
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
                    showNotification(`Credentials copied for ${userName} (${companyName})`, 'success');
                } else {
                    showNotification(`Failed to copy credentials for ${userName} (${companyName})`, 'error');
                }
            } catch (err) {
                console.error('Fallback: Oops, unable to copy', err);
                showNotification(`Failed to copy credentials for ${userName} (${companyName})`, 'error');
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

        // Handle send credentials button clicks
        document.addEventListener('DOMContentLoaded', function() {
            // Attach event listeners to all send credentials buttons
            document.querySelectorAll('.send-credentials-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const userId = this.getAttribute('data-user-id');
                    const userName = this.getAttribute('data-user-name');
                    const userEmail = this.getAttribute('data-user-email');
                    
                    sendCredentials(userId, userName, userEmail, this);
                });
            });
        });

        function sendCredentials(userId, userName, userEmail, button) {
            console.log(userId, userName, userEmail, button);
            // Disable the button to prevent multiple clicks
            button.disabled = true;
            const originalHTML = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Sending...';
            
            // Send AJAX request
            fetch(`{{ url('/users/send-credentials') }}/${userId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message || `Credentials sent successfully to ${userEmail}`, 'success');
                } else {
                    showNotification(data.message || 'Failed to send credentials', 'error');
                }
            })
            .catch(error => {
                console.error('Error sending credentials:', error);
                showNotification('An error occurred while sending credentials', 'error');
            })
            .finally(() => {
                // Re-enable the button
                button.disabled = false;
                button.innerHTML = originalHTML;
            });
        }
    </script>
@endsection
