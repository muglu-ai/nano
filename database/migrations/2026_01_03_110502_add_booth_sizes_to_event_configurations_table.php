<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_configurations', function (Blueprint $table) {
            if (!Schema::hasColumn('event_configurations', 'booth_sizes')) {
                $table->json('booth_sizes')->nullable()->after('gst_rate');
            }
        });

        // Set default booth sizes for existing records
        $defaultBoothSizes = [
            'Raw' => ['36', '48', '54', '72', '108', '135'],
            'Shell' => ['9', '12', '15', '18', '27']
        ];

        DB::table('event_configurations')->whereNull('booth_sizes')->update([
            'booth_sizes' => json_encode($defaultBoothSizes)
        ]);
    }

    public function down(): void
    {
        Schema::table('event_configurations', function (Blueprint $table) {
            $table->dropColumn('booth_sizes');
        });
    }
};
