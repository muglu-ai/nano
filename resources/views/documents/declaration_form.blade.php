@extends('layouts.users')
@section('title', 'Declaration Form')
@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col">
                <h3 class="mb-0 h4 font-weight-bolder">Declaration Form</h3>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Download Declaration Form</h5>
                        <p class="card-text">Please download the declaration form, fill it out, sign it, and upload the signed document.</p>
                        <a href="{{ $pdfPath }}" target="_blank" class="btn btn-primary">
                            <i class="fas fa-download"></i> Download Declaration Form
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Upload Signed Declaration Form</h5>
                        @if(isset($application) && $application && $application->declarationStatus == 1)
                            <div class="alert alert-success">
                                <i class="fas fa-check-circle"></i> Declaration form has been uploaded successfully.
                            </div>
                        @endif
                        
                        <form action="{{ route('declaration.upload') }}" method="POST" enctype="multipart/form-data"
                              @if(isset($application) && $application && $application->declarationStatus == 1) style="pointer-events: none; opacity: 0.6;" @endif>
                            @csrf
                            <div class="form-group">
                                <label for="declaration_file">Select Signed PDF File</label>
                                <input type="file"
                                       class="form-control-file @error('declaration_file') is-invalid @enderror"
                                       id="declaration_file"
                                       name="declaration_file"
                                       accept="application/pdf"
                                       required
                                       @if(isset($application) && $application && $application->declarationStatus == 1) disabled @endif>
                                <small class="form-text text-muted">
                                    Maximum file size: 1MB. Only PDF files are allowed.
                                </small>
                                @error('declaration_file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-success"
                                    @if(isset($application) && $application && $application->declarationStatus == 1) disabled @endif>
                                <i class="fas fa-upload"></i> Upload Signed Declaration
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if(isset($uploadedFilePath) && $uploadedFilePath)
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-file-pdf"></i> Uploaded Declaration Form
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info mb-3">
                                <i class="fas fa-info-circle"></i> Your signed declaration form has been successfully uploaded.
                            </div>
                            <iframe src="{{ $uploadedFilePath }}" width="100%" height="600px" style="border:none;"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        @elseif(isset($pdfPath))
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Preview Declaration Form</h5>
                        </div>
                        <div class="card-body">
                            <iframe src="{{ $pdfPath }}" width="100%" height="600px" style="border:none;"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

