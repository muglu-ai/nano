@extends('layouts.dashboard')
@section('title', 'CCAvenue Transactions')
@section('content')

<style>
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
        color: white;
        border-radius: 0.35rem;
        padding: 0.75rem 1.5rem;
    }
    
    .search-btn:hover {
        background-color: #2e59d9;
        border-color: #2e59d9;
        color: white;
    }
    
    .clear-btn {
        background-color: #e74a3b;
        border-color: #e74a3b;
        color: white;
        border-radius: 0.35rem;
        padding: 0.75rem 1.5rem;
    }
    
    .clear-btn:hover {
        background-color: #c0392b;
        border-color: #c0392b;
        color: white;
    }
    
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
    
    .badge {
        padding: 0.5rem 0.75rem;
        font-weight: 600;
        border-radius: 0.35rem;
    }
    
    .badge-success {
        background-color: #1cc88a;
        color: white;
    }
    
    .badge-danger {
        background-color: #e74a3b;
        color: white;
    }
    
    .badge-warning {
        background-color: #f6c23e;
        color: #5a5c69;
    }
    
    .badge-info {
        background-color: #36b9cc;
        color: white;
    }
</style>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h5 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-credit-card"></i> CCAvenue Payment Transactions
            </h5>
        </div>
        <div class="card-body">
            <!-- Search and Filters -->
            <div class="search-section">
                <form method="GET" action="{{ route('admin.ccavenue.transactions') }}" class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control search-input" 
                               placeholder="Search by TIN, Order ID, Transaction ID, Email..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-control search-input">
                            <option value="">All Status</option>
                            <option value="Success" {{ request('status') == 'Success' ? 'selected' : '' }}>Success</option>
                            <option value="Failed" {{ request('status') == 'Failed' ? 'selected' : '' }}>Failed</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="from_date" class="form-control search-input" 
                               value="{{ request('from_date') }}" placeholder="From Date">
                    </div>
                    <div class="col-md-2">
                        <input type="date" name="to_date" class="form-control search-input" 
                               value="{{ request('to_date') }}" placeholder="To Date">
                    </div>
                    <div class="col-md-2">
                        <select name="payment_method" class="form-control search-input">
                            <option value="">All Methods</option>
                            <option value="Credit Card" {{ request('payment_method') == 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
                            <option value="Net Banking" {{ request('payment_method') == 'Net Banking' ? 'selected' : '' }}>Net Banking</option>
                            <option value="Debit Card" {{ request('payment_method') == 'Debit Card' ? 'selected' : '' }}>Debit Card</option>
                            <option value="Unified Payments" {{ request('payment_method') == 'Unified Payments' ? 'selected' : '' }}>UPI</option>
                            <option value="Wallet" {{ request('payment_method') == 'Wallet' ? 'selected' : '' }}>Wallet</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn search-btn w-100">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <div class="col-md-12 mt-2">
                        <a href="{{ route('admin.ccavenue.transactions') }}" class="btn clear-btn">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                        <span class="ml-3 text-muted">
                            Total: {{ $transactions->total() }} transactions
                        </span>
                    </div>
                </form>
            </div>

            <!-- Transactions Table -->
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Order ID / TIN</th>
                            <th>Application ID</th>
                            <th>Company Name</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Payment Method</th>
                            <th>Transaction ID</th>
                            <th>Bank Ref No</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $transaction)
                        <tr>
                            <td>
                                <strong>{{ $transaction->order_id }}</strong><br>
                                <small class="text-muted">TIN: {{ $transaction->tin_number ?? 'N/A' }}</small>
                            </td>
                            <td>
                                @if(isset($transaction->application_id))
                                    <a href="{{ route('application.show.admin', ['id' => $transaction->application->id ?? '']) }}" 
                                       class="text-primary">
                                        {{ $transaction->application_id }}
                                    </a>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td>
                                {{ $transaction->company_name ?? 'N/A' }}
                            </td>
                            <td>
                                <strong>{{ $transaction->currency ?? 'INR' }} {{ number_format($transaction->amount, 2) }}</strong>
                            </td>
                            <td>
                                @if($transaction->status == 'Success')
                                    <span class="badge badge-success">Success</span>
                                @elseif($transaction->status == 'Failed')
                                    <span class="badge badge-danger">Failed</span>
                                @elseif($transaction->status == 'Pending')
                                    <span class="badge badge-warning">Pending</span>
                                @else
                                    <span class="badge badge-info">{{ $transaction->status ?? 'N/A' }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $transaction->payment_method ?? 'N/A' }}
                            </td>
                            <td>
                                <small>{{ $transaction->transaction_id ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <small>{{ $transaction->bank_ref_no ?? 'N/A' }}</small>
                            </td>
                            <td>
                                @if($transaction->trans_date)
                                    {{ \Carbon\Carbon::parse($transaction->trans_date)->format('d M Y H:i') }}
                                @else
                                    {{ \Carbon\Carbon::parse($transaction->created_at)->format('d M Y H:i') }}
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info" 
                                        onclick="showTransactionDetails({{ $transaction->id }})"
                                        title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <p class="text-muted">No transactions found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} 
                    of {{ $transactions->total() }} transactions
                </div>
                <div>
                    {{ $transactions->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transaction Details Modal -->
<div class="modal fade" id="transactionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Transaction Details</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="transactionDetails">
                <p>Loading...</p>
            </div>
        </div>
    </div>
</div>

<script>
function showTransactionDetails(transactionId) {
    // Fetch transaction details via AJAX
    fetch(`/admin/ccavenue-transactions/${transactionId}/details`, {
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const transaction = data.transaction;
            let html = `
                <table class="table table-bordered">
                    <tr><th>Order ID</th><td>${transaction.order_id || 'N/A'}</td></tr>
                    <tr><th>TIN Number</th><td>${transaction.tin_number || 'N/A'}</td></tr>
                    <tr><th>Application ID</th><td>${transaction.application_id || 'N/A'}</td></tr>
                    <tr><th>Company Name</th><td>${transaction.company_name || 'N/A'}</td></tr>
                    <tr><th>Amount</th><td><strong>${transaction.currency || 'INR'} ${parseFloat(transaction.amount || 0).toFixed(2)}</strong></td></tr>
                    <tr><th>Status</th><td><span class="badge badge-${transaction.status === 'Success' ? 'success' : (transaction.status === 'Failed' ? 'danger' : 'warning')}">${transaction.status || 'N/A'}</span></td></tr>
                    <tr><th>Payment Method</th><td>${transaction.payment_method || 'N/A'}</td></tr>
                    <tr><th>Transaction ID</th><td>${transaction.transaction_id || 'N/A'}</td></tr>
                    <tr><th>Bank Ref No</th><td>${transaction.bank_ref_no || 'N/A'}</td></tr>
                    <tr><th>Reference ID</th><td>${transaction.reference_id || 'N/A'}</td></tr>
                    <tr><th>Email</th><td>${transaction.email || 'N/A'}</td></tr>
                    <tr><th>Transaction Date</th><td>${transaction.trans_date || transaction.created_at || 'N/A'}</td></tr>
            `;
            
            if (transaction.response_json) {
                const responseData = typeof transaction.response_json === 'string' 
                    ? JSON.parse(transaction.response_json) 
                    : transaction.response_json;
                html += `
                    <tr><th colspan="2"><strong>Full Response Data</strong></th></tr>
                    <tr><td colspan="2"><pre style="max-height: 300px; overflow-y: auto;">${JSON.stringify(responseData, null, 2)}</pre></td></tr>
                `;
            }
            
            html += `</table>`;
            document.getElementById('transactionDetails').innerHTML = html;
            $('#transactionModal').modal('show');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to load transaction details');
    });
}
</script>

@endsection
