<?php

/**
 * Quick Route Testing Script
 * Run: php test-routes.php
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TICKET ROUTES TEST ===\n\n";

// Get event info
try {
    $event = \App\Models\Events::first();
    if ($event) {
        echo "📅 Event Found:\n";
        echo "   ID: {$event->id}\n";
        echo "   Name: {$event->event_name}\n";
        echo "   Slug: " . ($event->slug ?: 'NOT SET') . "\n";
        echo "   Year: {$event->event_year}\n\n";
    } else {
        echo "⚠️  No events found in database\n\n";
    }
} catch (\Exception $e) {
    echo "⚠️  Error loading event: " . $e->getMessage() . "\n\n";
}

// Test Admin Routes
echo "🔐 ADMIN ROUTES (Requires Login):\n";
echo "   Base URL: " . config('app.url') . "\n\n";

$adminRoutes = [
    'admin.tickets.events' => '/admin/tickets/events',
    'admin.tickets.events.setup' => '/admin/tickets/events/{eventId}/setup',
    'admin.tickets.events.days' => '/admin/tickets/events/{eventId}/days',
    'admin.tickets.events.registration-categories' => '/admin/tickets/events/{eventId}/registration-categories',
    'admin.tickets.events.categories' => '/admin/tickets/events/{eventId}/categories',
    'admin.tickets.events.ticket-types' => '/admin/tickets/events/{eventId}/ticket-types',
    'admin.tickets.events.rules' => '/admin/tickets/events/{eventId}/rules',
];

foreach ($adminRoutes as $name => $path) {
    $testPath = str_replace('{eventId}', $event->id ?? 1, $path);
    $fullUrl = config('app.url') . $testPath;
    echo "   ✓ {$name}\n";
    echo "     URL: {$fullUrl}\n";
    echo "     Path: {$testPath}\n\n";
}

// Test Public Routes
echo "🌐 PUBLIC ROUTES (No Login Required):\n\n";

$publicRoutes = [
    'tickets.discover' => '/tickets/{eventSlug}',
    'tickets.register' => '/tickets/{eventSlug}/register',
    'tickets.manage' => '/manage-booking/{token}',
    'tickets.payment' => '/ticket-payment/{orderId}',
];

foreach ($publicRoutes as $name => $path) {
    if (strpos($path, '{eventSlug}') !== false) {
        $testPath = str_replace('{eventSlug}', $event->slug ?: $event->id ?? 'bts-2026', $path);
    } else {
        $testPath = $path;
    }
    $fullUrl = config('app.url') . $testPath;
    echo "   ✓ {$name}\n";
    echo "     URL: {$fullUrl}\n";
    echo "     Path: {$testPath}\n\n";
}

echo "=== TESTING INSTRUCTIONS ===\n\n";
echo "1. Clear cache first:\n";
echo "   php artisan optimize:clear\n\n";
echo "2. Login as admin:\n";
echo "   Go to: " . config('app.url') . "/login\n\n";
echo "3. Test admin routes:\n";
echo "   Start with: " . config('app.url') . "/admin/tickets/events\n\n";
echo "4. Test public routes:\n";
echo "   Try: " . config('app.url') . "/tickets/" . ($event->slug ?: $event->id ?? 'bts-2026') . "\n\n";

