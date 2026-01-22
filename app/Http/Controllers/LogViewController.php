<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class LogViewController extends Controller
{
    public function __construct()
    {
        $this->middleware(['admin']);
    }

    /**
     * Display log viewer page
     */
    public function index(Request $request)
    {
        $logFile = storage_path('logs/laravel.log');
        $lines = $request->get('lines', 500); // Default to last 500 lines
        $filter = $request->get('filter', ''); // Filter by keyword
        $level = $request->get('level', 'all'); // Filter by log level
        
        $logContent = [];
        
        if (File::exists($logFile)) {
            // Read the log file
            $content = File::get($logFile);
            
            // Split into lines
            $allLines = explode("\n", $content);
            
            // Get last N lines
            $recentLines = array_slice($allLines, -$lines);
            
            // Filter by keyword if provided
            if (!empty($filter)) {
                $recentLines = array_filter($recentLines, function($line) use ($filter) {
                    return stripos($line, $filter) !== false;
                });
            }
            
            // Filter by log level if provided
            if ($level !== 'all') {
                $recentLines = array_filter($recentLines, function($line) use ($level) {
                    return stripos($line, ".{$level}:") !== false || 
                           stripos($line, "[{$level}]") !== false ||
                           stripos($line, "{$level}") !== false;
                });
            }
            
            // Parse log entries
            foreach ($recentLines as $line) {
                if (empty(trim($line))) continue;
                
                $logContent[] = [
                    'raw' => $line,
                    'timestamp' => $this->extractTimestamp($line),
                    'level' => $this->extractLevel($line),
                    'message' => $this->extractMessage($line),
                ];
            }
            
            // Reverse to show newest first
            $logContent = array_reverse($logContent);
        }
        
        return view('admin.logs', [
            'logs' => $logContent,
            'totalLines' => File::exists($logFile) ? count(explode("\n", File::get($logFile))) : 0,
            'currentLines' => $lines,
            'filter' => $filter,
            'level' => $level,
        ]);
    }

    /**
     * Extract timestamp from log line
     */
    private function extractTimestamp($line)
    {
        // Laravel log format: [2024-01-22 10:15:34]
        if (preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $matches)) {
            return $matches[1];
        }
        return null;
    }

    /**
     * Extract log level from log line
     */
    private function extractLevel($line)
    {
        $levels = ['emergency', 'alert', 'critical', 'error', 'warning', 'notice', 'info', 'debug'];
        foreach ($levels as $level) {
            if (stripos($line, ".{$level}:") !== false || 
                stripos($line, "[{$level}]") !== false ||
                stripos($line, "{$level}") !== false) {
                return $level;
            }
        }
        return 'info';
    }

    /**
     * Extract message from log line
     */
    private function extractMessage($line)
    {
        // Remove timestamp and level, return the rest
        $message = preg_replace('/\[\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}\]/', '', $line);
        $message = preg_replace('/\.(emergency|alert|critical|error|warning|notice|info|debug):/', '', $message);
        return trim($message);
    }

    /**
     * Clear log file
     */
    public function clear(Request $request)
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (File::exists($logFile)) {
            File::put($logFile, '');
            Log::info('Log file cleared by admin', ['user_id' => auth()->id()]);
        }
        
        return redirect()->route('admin.logs')->with('success', 'Log file cleared successfully.');
    }

    /**
     * Download log file
     */
    public function download()
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (File::exists($logFile)) {
            return response()->download($logFile, 'laravel-' . date('Y-m-d') . '.log');
        }
        
        return redirect()->route('admin.logs')->with('error', 'Log file not found.');
    }
}
