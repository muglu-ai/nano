@extends('layouts.users')
@section('title', 'Document Viewer')
@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col">
                <h3 class="mb-0 h4 font-weight-bolder">Document</h3>
            </div>

        </div>
        
        
        @if(isset($pdfPath))
            <div class="row">
            <div class="col-12">
                <iframe src="{{ $pdfPath }}" width="100%" height="600px" style="border:none;"></iframe>
            </div>
            </div>
        @else
            <div class="alert alert-warning">
                PDF file not found.
            </div>
        @endif
    </div>
@endsection
