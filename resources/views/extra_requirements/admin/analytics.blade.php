@extends('layouts.dashboard')
@section('title', 'Extra Requirement Analytics')

@section('content')
<div class="container mt-4">
    <h2 class="mb-4">Purchased Items Analytics</h2>
    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Item Code</th>
                    <th>Item Name</th>
                    <th>Total Quantity Sold</th>
                    <th>Unit Price</th>
                    <th>Total Revenue (excl. GST)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $item)
                    <tr>
                        <td>{{ $item['item_code'] }}</td>
                        <td>{{ $item['name'] }}</td>
                        <td>{{ $item['quantity'] }}</td>
                        <td>₹{{ number_format($item['unit_price']) }}</td>
                        <td>₹{{ number_format($item['revenue'], 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">No items found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>




@endsection
