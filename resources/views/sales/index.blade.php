@extends('layouts.dashboard')
@section('title', 'Sales Report')

@section('content')

<style>

    th {
    text-align: left !important;
        padding-left:8px !important;
    }
    </style>
    <div class="container-fluid py-2">
        <div class="row">
            <!-- Total Revenue Card -->
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <p class="text-md font-weight-bold text-dark">Total Revenue</p>
                        <h5 class="font-weight-bolder">{{$currency}} {{ number_format($totalRevenue, 2) }}</h5>
{{--                        <span class="text-success">+55% since last month</span>--}}
                    </div>
                </div>
            </div>

            <!-- Date Range Filter -->
{{--            <div class="col-sm-4">--}}
{{--                <form method="GET" action="">--}}
{{--                    <div class="input-group">--}}
{{--                        <input type="date" name="start_date" value="{{ $startDate->toDateString() }}" class="form-control">--}}
{{--                        <input type="date" name="end_date" value="{{ $endDate->toDateString() }}" class="form-control">--}}
{{--                        <button type="submit" class="btn btn-primary">Filter</button>--}}
{{--                    </div>--}}
{{--                </form>--}}
{{--            </div>--}}
        </div>

        <!-- Revenue Breakdown -->
        <div class="row mt-3">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <p class="text-md font-weight-bold text-dark">Total Paid</p>
                        <h5>{{$currency}} {{ number_format($totalPaid, 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <p class="text-md font-weight-bold text-dark">Total Unpaid</p>
                        <h5>{{$currency}} {{ number_format($totalUnpaid, 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <p class="text-md font-weight-bold text-dark">Total Overdue</p>
                        <h5>{{$currency}} {{ number_format($totalOverdue, 2) }}</h5>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <p class="text-md font-weight-bold text-dark">Total Partial</p>
                        <h5>{{$currency}} {{ number_format($totalPartial, 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Table -->
        <div class="row mt-4">
            <div class="col">
                <div class="card">
                    <div class="card-header">
                        <h4 class="text-lg">Invoices</h4>
                    </div>
                    <div class="card-body">


                            <div class="table-responsive">
                        <table class="table table-flush">
                            <thead class="thead-light table-dark">
                            <tr>
                                <th class="text-left text-uppercase">Invoice No</th>
                                <th class="text-left text-uppercase">Invoice Type</th>
                                <th class="text-left text-uppercase">Amount</th>
                                <th class="text-left text-uppercase text-center">Status</th>
                                <th class="text-left text-uppercase">Date</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($invoices as $invoice)
                                <tr>
                                    <td class="text-dark text-md">{{ $invoice->invoice_no }}</td>
                                    <td class=" text-dark text-md">{{ $invoice->type }}</td>
                                    <td class=" text-dark text-md">{{$invoice->currency}} {{ number_format($invoice->total_final_price, 2) }}</td>
                                    <td class=" text-dark text-md"><span class=" badge d-block w-90 bg-{{ $invoice->payment_status == 'paid' ? 'success' : 'danger' }}">{{ ucfirst($invoice->payment_status) }}</span></td>
                                    <td class=" text-dark text-md">{{ $invoice->payment_due_date }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
