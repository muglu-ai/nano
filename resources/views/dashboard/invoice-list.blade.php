@extends('layouts.dashboard')
@section('title', 'Invoice List')
@section('content')
    <div class="container-fluid py-2">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <!-- Card header -->
                    <div class="card-header pb-0">
                        <div class="d-lg-flex">
                            <div>
                                <h5 class="mb-0">All {{$slug}}</h5>
                                <p class="text-sm mb-0">
                                    List of all {{$slug}}.
                                </p>
                            </div>
                            <div class="ms-auto my-auto mt-lg-0 mt-4">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    

@endsection
