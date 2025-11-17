<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/**
 * Simple remote runner to execute export_exhibitors_pdf_nodjango.py and keep history in JSON.
 * Place this file on a server that supports Python and PHP.
 * Ensure the Python script path and exports directory are correct below.
 */

declare(strict_types=1);

header('Content-Type: application/json');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

// Basic shared secret support (optional)
$SHARED_SECRET = getenv('EXPORT_SHARED_SECRET') ?: ''; // set in server env or leave empty to disable
if (!empty($SHARED_SECRET)) {
    $provided = $_SERVER['HTTP_X_EXPORT_SECRET'] ?? ($_GET['secret'] ?? '');
    if (!hash_equals($SHARED_SECRET, (string)$provided)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
    }
}

$baseDir = __DIR__; // directory where this PHP file lives
$scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$baseUrl = $scheme . '://' . $host;
$python = 'python3'; // adjust if needed ('python' on Windows)
$scriptPath = realpath($baseDir . '/../../export_exhibitors_pdf_nodjango.py'); // adjust if script is elsewhere
$exportsDir = $baseDir . '/exports';
$historyFile = $baseDir . '/export_history.json';

// Ensure exports directory exists
if (!is_dir($exportsDir) && !mkdir($exportsDir, 0755, true) && !is_dir($exportsDir)) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Cannot create exports directory']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? 'run';

function read_history(string $historyFile): array {
    if (!file_exists($historyFile)) {
        return [];
    }
    $json = @file_get_contents($historyFile);
    if ($json === false || trim($json) === '') {
        return [];
    }
    $data = json_decode($json, true);
    return is_array($data) ? $data : [];
}

function write_history(string $historyFile, array $entry): void {
    $all = read_history($historyFile);
    $all[] = $entry;
    $fp = fopen($historyFile, 'c+');
    if ($fp) {
        // exclusive lock
        if (flock($fp, LOCK_EX)) {
            ftruncate($fp, 0);
            fwrite($fp, json_encode($all, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
            fflush($fp);
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    } else {
        // fallback: best-effort write
        @file_put_contents($historyFile, json_encode($all, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
}

if ($action === 'history') {
    $hist = array_reverse(read_history($historyFile));
    echo json_encode(['success' => true, 'history' => $hist]);
    exit;
}

if ($action === 'run') {
    if (!file_exists($scriptPath)) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Python script not found']);
        exit;
    }

    $startedAt = date('Y-m-d H:i:s');
    $startTs = microtime(true);

    $cmd = sprintf(
        '%s %s --out %s 2>&1',
        escapeshellcmd($python),
        escapeshellarg($scriptPath),
        escapeshellarg($exportsDir)
    );

    $output = shell_exec($cmd);
    $finishedAt = date('Y-m-d H:i:s');
    $duration = max(0, microtime(true) - $startTs);

    $success = false;
    $filename = null;
    $url = null;
    $timestamp = null;

    if (is_string($output) && preg_match('/PDF generated:\s*(.+)\s*$/m', $output, $m)) {
        $absPath = trim($m[1]);
        if (file_exists($absPath)) {
            $success = true;
            $filename = basename($absPath);
            // Public absolute URL
            $url = $baseUrl . '/tools/exports/' . $filename;
            if (preg_match('/BTS_Exhibitor_Directory_(\d{14})\.pdf$/', $filename, $tm)) {
                $timestamp = $tm[1];
            }
        }
    } else {
        // attempt latest as fallback
        $files = glob($exportsDir . DIRECTORY_SEPARATOR . 'BTS_Exhibitor_Directory_*.pdf');
        if (!empty($files)) {
            rsort($files);
            $latest = $files[0];
            $success = true;
            $filename = basename($latest);
            $url = $baseUrl . '/tools/exports/' . $filename;
            if (preg_match('/BTS_Exhibitor_Directory_(\d{14})\.pdf$/', $filename, $tm)) {
                $timestamp = $tm[1];
            }
        }
    }

    $entry = [
        'started_at' => $startedAt,
        'finished_at' => $finishedAt,
        'duration_seconds' => round($duration, 2),
        'success' => $success,
        'filename' => $filename,
        'url' => $url,
        'timestamp' => $timestamp,
    ];
    // keep a truncated output for debugging
    if (!empty($output)) {
        $entry['output_tail'] = substr($output, -2000);
    }

    write_history($historyFile, $entry);

    if ($success) {
        echo json_encode(array_merge(['success' => true], $entry));
    } else {
        http_response_code(500);
        echo json_encode(array_merge(['success' => false, 'message' => 'PDF generation failed'], $entry));
    }
    exit;
}

// Default: show basic usage
echo json_encode([
    'success' => true,
    'message' => 'Use action=run to generate or action=history to view history.',
]);

