@extends('layouts.dashboard')

@section('title', 'Log Viewer')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Laravel Log Viewer</h3>
                    <div>
                        <a href="{{ route('admin.logs.download') }}" class="btn btn-sm btn-primary">
                            <i class="fas fa-download"></i> Download Log
                        </a>
                        <form action="{{ route('admin.logs.clear') }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to clear the log file?');">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="fas fa-trash"></i> Clear Log
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" action="{{ route('admin.logs') }}" class="mb-3">
                        <div class="row">
                            <div class="col-md-3">
                                <label for="lines">Lines to Show:</label>
                                <select name="lines" id="lines" class="form-control">
                                    <option value="100" {{ $currentLines == 100 ? 'selected' : '' }}>Last 100 lines</option>
                                    <option value="500" {{ $currentLines == 500 ? 'selected' : '' }}>Last 500 lines</option>
                                    <option value="1000" {{ $currentLines == 1000 ? 'selected' : '' }}>Last 1000 lines</option>
                                    <option value="2000" {{ $currentLines == 2000 ? 'selected' : '' }}>Last 2000 lines</option>
                                    <option value="5000" {{ $currentLines == 5000 ? 'selected' : '' }}>Last 5000 lines</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="level">Log Level:</label>
                                <select name="level" id="level" class="form-control">
                                    <option value="all" {{ $level == 'all' ? 'selected' : '' }}>All Levels</option>
                                    <option value="error" {{ $level == 'error' ? 'selected' : '' }}>Error</option>
                                    <option value="warning" {{ $level == 'warning' ? 'selected' : '' }}>Warning</option>
                                    <option value="info" {{ $level == 'info' ? 'selected' : '' }}>Info</option>
                                    <option value="debug" {{ $level == 'debug' ? 'selected' : '' }}>Debug</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="filter">Search (Keyword):</label>
                                <input type="text" name="filter" id="filter" class="form-control" 
                                       value="{{ $filter }}" placeholder="e.g., Poster Payment, email, error...">
                            </div>
                            <div class="col-md-2">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search"></i> Filter
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Log Stats -->
                    <div class="alert alert-info">
                        <strong>Total Lines in Log:</strong> {{ number_format($totalLines) }} | 
                        <strong>Showing:</strong> {{ count($logs) }} entries
                        @if($filter)
                            | <strong>Filtered by:</strong> "{{ $filter }}"
                        @endif
                        @if($level != 'all')
                            | <strong>Level:</strong> {{ ucfirst($level) }}
                        @endif
                    </div>

                    <!-- Log Content -->
                    <div class="log-container" style="max-height: 600px; overflow-y: auto; background: #1e1e1e; color: #d4d4d4; padding: 15px; border-radius: 4px; font-family: 'Courier New', monospace; font-size: 12px;">
                        @if(empty($logs))
                            <div class="text-center text-muted py-5">
                                <i class="fas fa-info-circle fa-3x mb-3"></i>
                                <p>No log entries found.</p>
                                @if($filter || $level != 'all')
                                    <p>Try adjusting your filters.</p>
                                @endif
                            </div>
                        @else
                            @foreach($logs as $log)
                                <div class="log-entry mb-2" style="border-left: 3px solid 
                                    @if($log['level'] == 'error') #dc3545
                                    @elseif($log['level'] == 'warning') #ffc107
                                    @elseif($log['level'] == 'info') #17a2b8
                                    @elseif($log['level'] == 'debug') #6c757d
                                    @else #28a745
                                    @endif; padding-left: 10px;">
                                    @if($log['timestamp'])
                                        <span style="color: #569cd6;">[{{ $log['timestamp'] }}]</span>
                                    @endif
                                    <span style="color: 
                                        @if($log['level'] == 'error') #f48771
                                        @elseif($log['level'] == 'warning') #dcdcaa
                                        @elseif($log['level'] == 'info') #4ec9b0
                                        @elseif($log['level'] == 'debug') #808080
                                        @else #b5cea8
                                        @endif; font-weight: bold; text-transform: uppercase; margin: 0 5px;">
                                        {{ $log['level'] }}
                                    </span>
                                    <span style="color: #d4d4d4;">
                                        {!! htmlspecialchars($log['message']) !!}
                                    </span>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <!-- Quick Filters -->
                    <div class="mt-3">
                        <strong>Quick Filters:</strong>
                        <a href="{{ route('admin.logs', ['filter' => 'Poster Payment', 'lines' => $currentLines]) }}" class="btn btn-sm btn-outline-primary">Poster Payment</a>
                        <a href="{{ route('admin.logs', ['filter' => 'email', 'lines' => $currentLines]) }}" class="btn btn-sm btn-outline-primary">Email</a>
                        <a href="{{ route('admin.logs', ['filter' => 'error', 'level' => 'error', 'lines' => $currentLines]) }}" class="btn btn-sm btn-outline-danger">Errors Only</a>
                        <a href="{{ route('admin.logs', ['filter' => 'Payment', 'lines' => $currentLines]) }}" class="btn btn-sm btn-outline-success">Payment</a>
                        <a href="{{ route('admin.logs', ['lines' => $currentLines]) }}" class="btn btn-sm btn-outline-secondary">Clear Filters</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .log-container {
        scrollbar-width: thin;
        scrollbar-color: #555 #1e1e1e;
    }
    
    .log-container::-webkit-scrollbar {
        width: 8px;
    }
    
    .log-container::-webkit-scrollbar-track {
        background: #1e1e1e;
    }
    
    .log-container::-webkit-scrollbar-thumb {
        background: #555;
        border-radius: 4px;
    }
    
    .log-container::-webkit-scrollbar-thumb:hover {
        background: #777;
    }
    
    .log-entry {
        word-wrap: break-word;
        white-space: pre-wrap;
    }
    
    .log-entry:hover {
        background: rgba(255, 255, 255, 0.05);
    }
</style>
@endsection
