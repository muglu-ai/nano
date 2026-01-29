<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('poster_authors', function (Blueprint $table) {
            if (!Schema::hasColumn('poster_authors', 'title')) {
                $table->string('title', 10)->nullable()->after('author_index');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('poster_authors', function (Blueprint $table) {
            $table->dropColumn('title');
        });
    }
};
