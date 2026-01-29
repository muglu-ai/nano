@extends('layouts.users')
@section('title', $slug)

@section('content')
<div class="container mt-4">
    <h3>Upload Press Release</h3>
    <form action="{{ route('press.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label>Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Press Note (PDF / DOC)</label>
            <input type="file" name="file" class="form-control">
        </div>

        <div class="mb-3">
            <label>Description / Summary</label>
            <textarea name="summary" class="form-control" rows="4"></textarea>
        </div>

        <button type="submit" class="btn btn-info">Upload</button>
    </form>
</div>
@endsection