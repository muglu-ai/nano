@extends('layouts.dashboard')
@section('title', 'Invoices and Payments')
@section('content')
    <div class="container-fluid py-2">
        <div class="row">
            <div class="ms-3">
                <h3 class="mb-0 h4 font-weight-bolder">@yield('title')</h3>
                <p class="mb-4">
                    Invoices and Payments
                </p>
            </div>
        </div>
    </div>
    <div class="container-fluid py-2">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h6>Invoices</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">Company Name</th>
                                        <th scope="col">Category</th>
                                        <th scope="col">Total Amount</th>
                                        <th scope="col">Paid Amount</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Date</th>
                                        <th scope="col">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($applications as $application)
                                    @foreach($application->invoices as $invoice)
{{--                                        @dd($invoice)--}}
                                        <tr>
                                            <td>{{ $application->billingDetail->billing_company }}</td>
                                            <td>{{ $application->application_id ?? 'N/A' }}</td>
                                            <td>{{ $application->billingDetail->billing_company ?? 'N/A' }}</td>

                                            <td> &#8377; {{  number_format($invoice->amount, 2) }}</td>
                                            <td>
                                    <span class="badge
                                        {{ $invoice->payment_status === 'paid' ? 'bg-success' : ($invoice->status === 'pending' ? 'bg-warning' : 'bg-danger') }}">
                                        {{ ucfirst($invoice->payment_status) }}
                                    </span>
                                            </td>
                                            <td>{{ $invoice->created_at->format('Y-m-d') }}</td>
                                            <td>
                                                <a href="{{ route('invoice.show', ['id' => $invoice->invoice_no]) }}" class="btn btn-primary">View Receipt</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No invoices found.</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>




@endsection
