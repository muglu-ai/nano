<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_configurations', function (Blueprint $table) {
            $table->id();
            $table->string('event_name')->default('Bengaluru Tech Summit');
            $table->string('event_year')->default('2026');
            $table->string('short_name')->default('BTS');
            $table->string('event_website')->nullable();
            $table->string('event_date_start')->nullable();
            $table->string('event_date_end')->nullable();
            $table->text('event_venue')->nullable();
            $table->string('organizer_name')->nullable();
            $table->string('organizer_email')->nullable();
            $table->string('organizer_phone')->nullable();
            $table->string('organizer_website')->nullable();
            $table->text('organizer_address')->nullable();
            $table->string('organizer_logo')->nullable();
            $table->string('event_logo')->nullable();
            $table->string('app_url')->nullable();
            $table->decimal('shell_scheme_rate', 10, 2)->default(13000);
            $table->decimal('raw_space_rate', 10, 2)->default(12000);
            $table->decimal('ind_processing_charge', 5, 2)->default(3);
            $table->decimal('int_processing_charge', 5, 2)->default(9);
            $table->decimal('gst_rate', 5, 2)->default(18);
            $table->json('social_links')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_configurations');
    }
};
