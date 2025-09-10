@extends('layouts.dashboard')
@section('title', 'File Upload')
@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Upload File</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('file.upload') }}" method="POST" enctype="multipart/form-data" 
                          id="uploadForm">
                        @csrf
                        <div class="mb-3">
                            <label for="file" class="form-label">Choose File</label>
                            <input type="file" class="form-control" id="file" name="file" required>
                            <div class="form-text">Maximum file size: 1GB</div>
                            <div class="progress mt-2 d-none" id="uploadProgress">
                                <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Upload</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('file');
    const file = fileInput.files[0];
    const maxSize = 1024 * 1024 * 1024; // 1GB in bytes

    if (file && file.size > maxSize) {
        e.preventDefault();
        alert('File size exceeds 1GB limit');
        return false;
    }

    const progress = document.getElementById('uploadProgress');
    progress.classList.remove('d-none');
});
</script>
@endsection