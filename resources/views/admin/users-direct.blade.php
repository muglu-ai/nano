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
        .search-container {
            margin-bottom: 0;
            padding: 20px 0;
        }
        .sortable {
            cursor: pointer;
        }
        .sortable:hover {
            background-color: #333;
        }
        
        /* Enhanced search box styling */
        .input-group-lg .form-control {
            font-size: 1.1rem;
            padding: 0.75rem 1rem;
        }
        .input-group-text {
            border-right: none;
        }
        .form-control:focus {
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        .search-container .input-group {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .search-container .btn {
            border-radius: 0;
        }
        .search-container .input-group-text {
            border-radius: 0.5rem 0 0 0.5rem;
        }
        .search-container .btn:last-child {
            border-radius: 0 0.5rem 0.5rem 0;
        }
        
        /* Card header improvements */
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-bottom: none;
        }
        .card-header h5 {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        .card-header p {
            opacity: 0.9;
            margin-bottom: 0;
        }
        
        /* Table improvements */
        .table-responsive {
            border-radius: 0.5rem;
            overflow: hidden;
        }
        .table th {
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .table td {
            vertical-align: middle;
        }
        
        /* Pagination improvements */
        .pagination {
            margin-bottom: 0;
        }
        .page-link {
            border-radius: 0.375rem;
            margin: 0 2px;
            border: 1px solid #dee2e6;
        }
        .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }
    </style>

    <div class="container-fluid py-2">
        <div class="row mt-4">
            <div class="col-12">
                <div class="card shadow-lg border-0">
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
                                <span class="badge bg-light text-dark fs-6">
                                    <i class="fas fa-users me-1"></i>
                                    {{ $users->total() }} Total Users
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Search and Filters -->
                    <div class="card-body bg-light border-bottom">
                        <form method="GET" action="{{ route('users.list2') }}" class="search-container">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="input-group input-group-lg">
                                        <span class="input-group-text bg-primary text-white">
                                            <i class="fas fa-search"></i>
                                        </span>
                                        <input type="text" 
                                               class="form-control form-control-lg border-primary" 
                                               name="search" 
                                               placeholder="Search by name, email, or company..." 
                                               value="{{ request('search') }}"
                                               style="border-left: none; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                        <button class="btn btn-primary btn-lg" type="submit">
                                            <i class="fas fa-search me-1"></i> Search
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <span class="input-group-text bg-secondary text-white">
                                            <i class="fas fa-list"></i>
                                        </span>
                                        <select name="per_page" class="form-select border-secondary" onchange="this.form.submit()">
                                            <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5 per page</option>
                                            <option value="10" {{ request('per_page') == 10 || !request('per_page') ? 'selected' : '' }}>10 per page</option>
                                            <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 per page</option>
                                            <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 per page</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3 text-end">
                                    @if(request('search'))
                                        <a href="{{ route('users.list2') }}" class="btn btn-outline-danger btn-lg">
                                            <i class="fas fa-times me-1"></i> Clear Search
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive min-vh-40" style="height: 500px;">
                        <table class="table table-flush min-vh-40" id="datatable-basic2">
                            <thead class="thead-light table-dark custom-header">
                            <tr>
                                <th class="text-uppercase text-md text-white sortable" 
                                    data-sort="company"
                                    onclick="sortTable('company')">
                                    Company
                                    @if(request('sort') == 'company')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th class="text-uppercase text-md text-white sortable" 
                                    data-sort="name"
                                    onclick="sortTable('name')">
                                    Name
                                    @if(request('sort') == 'name')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th class="text-uppercase text-md text-white sortable" 
                                    data-sort="email"
                                    onclick="sortTable('email')">
                                    Email
                                    @if(request('sort') == 'email')
                                        <i class="fas fa-sort-{{ request('direction') == 'asc' ? 'up' : 'down' }}"></i>
                                    @endif
                                </th>
                                <th class="text-uppercase text-md text-white" data-sort="phone">Password</th>
                                <th class="text-uppercase text-md text-white">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td class="text-md font-weight-normal text-dark">{{ $user->company ?? 'N/A' }}</td>
                                        <td class="text-md font-weight-normal text-dark">{{ $user->name ?? 'N/A' }}</td>
                                        <td class="text-md font-weight-normal text-dark">{{ $user->email ?? 'N/A' }}</td>
                                        <td class="text-md font-weight-normal text-dark">{{ $user->simplePass ?? 'N/A' }}</td>
                                        <td class="text-md font-weight-normal text-dark">
                                            <button class="btn btn-sm btn-primary" 
                                                    onclick="copyCredentials('{{ env("APP_URL") }}', '{{ $user->email }}', '{{ $user->simplePass }}', '{{ $user->name }}', '{{ $user->company }}')">
                                                <i class="fas fa-copy"></i> Copy Credentials
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            @if(request('search'))
                                                No users found matching your search criteria.
                                            @else
                                                No users found.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} results
                                @if(request('search'))
                                    <span class="badge bg-primary ms-2">
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
    </script>
@endsection
