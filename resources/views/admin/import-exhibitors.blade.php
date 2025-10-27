@extends('layouts.dashboard')
@section('title', 'Bulk Import Exhibitors')
@section('content')

    <div class="container-fluid py-2">
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">@yield('title')</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Instructions:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Download the sample CSV template</li>
                                <li>Fill in the exhibitor information</li>
                                <li>Upload the CSV file</li>
                                <li>The system will automatically create users and send credentials via email</li>
                            </ul>
                        </div>

                        <form id="importForm" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="csv_file" class="form-label">Select CSV File</label>
                                <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv,.txt" required>
                                <small class="form-text text-muted">Only CSV files are allowed</small>
                            </div>

                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary" id="importBtn">
                                    <i class="fas fa-upload me-2"></i> Import Exhibitors
                                </button>
                                <a href="{{ asset('sample_exhibitor_import.csv') }}" class="btn btn-outline-primary ms-2" download>
                                    <i class="fas fa-download me-2"></i> Download Sample CSV
                                </a>
                            </div>
                        </form>

                        <div id="importResults" style="display: none;">
                            <div class="alert" id="resultAlert">
                                <div id="resultMessage"></div>
                                <ul id="errorList" class="mb-0 mt-2"></ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sample Data Preview -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Expected CSV Format</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Field</th>
                                        <th>Required</th>
                                        <th>Example</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Organisation (Exhibitor Name)</td>
                                        <td><span class="badge bg-danger">Yes</span></td>
                                        <td>Intel Corporation</td>
                                    </tr>
                                    <tr>
                                        <td>Entity is Sponsor/ Exhibitor / Startup?</td>
                                        <td><span class="badge bg-secondary">No</span></td>
                                        <td>Exhibitor, Sponsor, or Startup</td>
                                    </tr>
                                    <tr>
                                        <td>Exhibition booth Size: in SQM</td>
                                        <td><span class="badge bg-secondary">No</span></td>
                                        <td>9, 18, 27, 36, 54</td>
                                    </tr>
                                    <tr>
                                        <td>Exhibitions Space Type (Raw / Shell)</td>
                                        <td><span class="badge bg-secondary">No</span></td>
                                        <td>Raw Space or Shell Scheme</td>
                                    </tr>
                                    <tr>
                                        <td>Exhibitor Contact Person Email *</td>
                                        <td><span class="badge bg-danger">Yes</span></td>
                                        <td>contact@example.com</td>
                                    </tr>
                                    <tr>
                                        <td>Exhibitor Contact Person Mobile *</td>
                                        <td><span class="badge bg-danger">Yes</span></td>
                                        <td>9876543210</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('importForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            const fileInput = document.getElementById('csv_file');
            
            if (!fileInput.files.length) {
                alert('Please select a CSV file');
                return;
            }
            
            formData.append('csv_file', fileInput.files[0]);
            formData.append('_token', '{{ csrf_token() }}');
            
            // Disable button and show loading
            const importBtn = document.getElementById('importBtn');
            importBtn.disabled = true;
            importBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Importing...';
            
            // Hide previous results
            document.getElementById('importResults').style.display = 'none';
            
            fetch('{{ route("admin.import.exhibitors") }}', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                importBtn.disabled = false;
                importBtn.innerHTML = '<i class="fas fa-upload me-2"></i> Import Exhibitors';
                
                // Show results
                const resultsDiv = document.getElementById('importResults');
                resultsDiv.style.display = 'block';
                
                const alert = document.getElementById('resultAlert');
                const message = document.getElementById('resultMessage');
                const errorList = document.getElementById('errorList');
                
                if (data.success) {
                    alert.className = 'alert alert-success';
                    message.innerHTML = `<strong>${data.message}</strong>`;
                    errorList.innerHTML = '';
                } else {
                    alert.className = 'alert alert-danger';
                    message.innerHTML = `<strong>${data.message}</strong>`;
                    
                    // Display errors if they exist
                    if (data.errors && data.errors.length > 0) {
                        let errorHtml = '<strong>Validation Errors:</strong><ul class="mt-2">';
                        data.errors.forEach(errorObj => {
                            if (typeof errorObj === 'object' && errorObj.errors) {
                                errorHtml += `<li><strong>Row ${errorObj.row}:</strong><ul>`;
                                errorObj.errors.forEach(err => {
                                    errorHtml += `<li>${err}</li>`;
                                });
                                errorHtml += '</ul></li>';
                            } else {
                                errorHtml += `<li>${errorObj}</li>`;
                            }
                        });
                        errorHtml += '</ul>';
                        errorList.innerHTML = errorHtml;
                    } else if (data.error) {
                        errorList.innerHTML = `<strong>Error:</strong> ${data.error}`;
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                importBtn.disabled = false;
                importBtn.innerHTML = '<i class="fas fa-upload me-2"></i> Import Exhibitors';
                
                const resultsDiv = document.getElementById('importResults');
                resultsDiv.style.display = 'block';
                document.getElementById('resultAlert').className = 'alert alert-danger';
                document.getElementById('resultMessage').textContent = 'An error occurred during import';
            });
        });
    </script>

@endsection

