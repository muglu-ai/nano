@extends('layouts.users')
@section('title', 'Exhibitor Directory Export')
@section('content')
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col">
                <h3 class="mb-0 h4 font-weight-bolder">Exhibitor Directory PDF Export</h3>
                <p class="text-muted mb-0">This will generate a consolidated Exhibitor Directory PDF. It can take a few minutes.</p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div id="statusArea">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="spinner-border text-primary" role="status" id="spinner">
                            <span class="visually-hidden">Generating...</span>
                        </div>
                        <div>
                            <div class="fw-bold" id="statusText">Preparing to generate PDF...</div>
                            <div class="text-muted small" id="timestampText"></div>
                        </div>
                    </div>
                    <div id="resultArea" style="display:none;">
                        <div class="alert alert-success mb-2" role="alert">
                            PDF generated successfully.
                        </div>
                        <a id="downloadLink" href="#" class="btn btn-primary" download>
                            <i class="fa-solid fa-download me-1"></i> Download PDF
                        </a>
                    </div>
                    <div id="errorArea" style="display:none;">
                        <div class="alert alert-danger" role="alert">
                            <span id="errorText">PDF generation failed.</span>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <button id="regenBtn" class="btn btn-outline-secondary btn-sm" type="button">
                        <i class="fa-solid fa-rotate me-1"></i> Generate Again
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function formatNow() {
            const d = new Date();
            const pad = n => String(n).padStart(2, '0');
            return `${d.getFullYear()}-${pad(d.getMonth()+1)}-${pad(d.getDate())} ${pad(d.getHours())}:${pad(d.getMinutes())}:${pad(d.getSeconds())}`;
        }

        async function runGeneration() {
            const spinner = document.getElementById('spinner');
            const statusText = document.getElementById('statusText');
            const timestampText = document.getElementById('timestampText');
            const resultArea = document.getElementById('resultArea');
            const errorArea = document.getElementById('errorArea');
            const errorText = document.getElementById('errorText');
            const downloadLink = document.getElementById('downloadLink');

            // Reset UI
            spinner.style.display = 'inline-block';
            resultArea.style.display = 'none';
            errorArea.style.display = 'none';
            statusText.textContent = 'Generating PDF...';
            timestampText.textContent = 'Started at: ' + formatNow();

            try {
                const resp = await fetch('{{ route('admin.exhibitors.exportDirectory.run') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });
                const data = await resp.json();
                spinner.style.display = 'none';

                if (data && data.success) {
                    statusText.textContent = 'PDF ready.';
                    resultArea.style.display = 'block';
                    if (data.filename) {
                        downloadLink.setAttribute('download', data.filename);
                    }
                    downloadLink.href = data.url.startsWith('http') ? data.url : (window.location.origin + data.url);
                    if (data.timestamp) {
                        timestampText.textContent = 'Completed at: ' + data.timestamp;
                    } else {
                        timestampText.textContent = 'Completed at: ' + formatNow();
                    }
                } else {
                    errorArea.style.display = 'block';
                    errorText.textContent = (data && data.message) ? data.message : 'PDF generation failed.';
                    statusText.textContent = 'Failed to generate PDF.';
                }
            } catch (e) {
                spinner.style.display = 'none';
                errorArea.style.display = 'block';
                errorText.textContent = e && e.message ? e.message : 'Unexpected error.';
                statusText.textContent = 'Failed to generate PDF.';
            }
        }

        document.getElementById('regenBtn').addEventListener('click', runGeneration);
        // Auto-run on page load
        window.addEventListener('DOMContentLoaded', runGeneration);
    </script>
@endsection

