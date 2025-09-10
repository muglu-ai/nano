@extends('layouts.dashboard')
@section('title', 'Master Requirement List')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <h1 class="mb-4">Master Requirement List</h1>
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Item ID</th>
                                <th>Item Name / Description</th>
                                <!-- <th></th> -->
                                <th>Price</th>
                                <th>Images</th>
                                <!-- <th>Available</th>
                                <th>Status</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($extraRequirements as $requirements)
                                <tr>
                                    <td>{{ $requirements->item_code }} </td>
                                    <td>{{ $requirements->item_name }} <br> {{ $requirements->size_or_description }}</td>
                                    <!-- <td></td> -->
                                    <td>{{ $requirements->price_for_expo}}</td>
                                    <td>
                                        @if($requirements->image)
                                            <img src="{{ asset($requirements->image) }}" alt="Image" >
                                        @else
                                            No Image
                                        @endif
                                    </td>
                                    <!-- <td>{{ $requirements->available_quantity}}</td>
                                    <td>{{ $requirements->status }}</td> -->
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection